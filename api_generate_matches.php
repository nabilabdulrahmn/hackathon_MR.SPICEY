<?php
set_time_limit(300);
// OUTPUT BUFFER: Captures any accidental whitespace, notices, or warnings
// that would corrupt the JSON response and cause the frontend loop.
ob_start();

include 'db.php';
set_time_limit(300); // Allow long execution for AI requests

// Suppress PHP notices/warnings from leaking into JSON output
error_reporting(0);

// Catch fatal errors (e.g. execution timeout) and output valid JSON
register_shutdown_function(function() {
    $err = error_get_last();
    if ($err && ($err['type'] & (E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR))) {
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'status'  => 'error',
            'message' => 'Server fatal error: ' . $err['message']
        ]);
    }
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$programme_id = (int)(isset($input['programme_id']) ? $input['programme_id'] : 0);

if (!$programme_id) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Programme ID required']);
    exit;
}

// 1. Secure API Key Loading via Service Account Credentials
function getGoogleAccessToken() {
    $credentials_path = __DIR__ . '/secure_keys/service-account-credentials.json';
    if (!file_exists($credentials_path)) {
        return null;
    }
    $json = file_get_contents($credentials_path);
    $creds = json_decode($json, true);
    if (!$creds) {
        return null;
    }

    $private_key  = isset($creds['private_key'])  ? $creds['private_key']  : '';
    $client_email = isset($creds['client_email']) ? $creds['client_email'] : '';
    $token_uri    = isset($creds['token_uri'])    ? $creds['token_uri']    : 'https://oauth2.googleapis.com/token';

    if (!$private_key || !$client_email) {
        return null;
    }

    $header  = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
    $now     = time();
    $payload = json_encode([
        'iss'   => $client_email,
        'sub'   => $client_email,
        'aud'   => $token_uri,
        'scope' => 'https://www.googleapis.com/auth/cloud-platform',
        'iat'   => $now,
        'exp'   => $now + 3600
    ]);

    $base64UrlHeader  = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    $signature_input  = $base64UrlHeader . "." . $base64UrlPayload;

    $signature = '';
    $success   = openssl_sign($signature_input, $signature, $private_key, "SHA256");
    if (!$success) {
        return null;
    }

    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    $jwt = $signature_input . "." . $base64UrlSignature;

    $ch = curl_init($token_uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'  => $jwt
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $res = json_decode($response, true);
        return isset($res['access_token']) ? $res['access_token'] : null;
    }
    return null;
}

$accessToken = getGoogleAccessToken();
$api_key     = $accessToken;

// 2. Fetch Programme and Rules
$query    = "SELECT p.*, t.rules_json, t.template_name
              FROM programmes p
              JOIN linkage_templates t ON p.template_id = t.id
              WHERE p.id = $programme_id AND p.status IN ('active', 'pending')";
$prog_res  = mysqli_query($conn, $query);
$programme = mysqli_fetch_assoc($prog_res);

if (!$programme) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Programme not found or not active']);
    exit;
}

$rules           = json_decode($programme['rules_json'], true);
$conf_threshold  = (float)(isset($rules['confidence_threshold']) ? $rules['confidence_threshold'] : 70);
$industry_focus  = isset($rules['industry_focus']) ? $rules['industry_focus'] : [];

// Pull programme-level metadata for the system prompt
$programme_name     = isset($programme['programme_name'])  ? $programme['programme_name']  : 'Unknown Programme';
$programme_location = isset($programme['location'])        ? $programme['location']         : 'Unknown Location';
$programme_stage    = isset($rules['stage_focus'])         ? $rules['stage_focus']          : 'Early Stage';
$template_name      = isset($programme['template_name'])   ? $programme['template_name']    : 'Standard Template';
$max_company_age    = isset($rules['max_company_age'])     ? $rules['max_company_age']      : 'N/A';
$max_revenue        = isset($rules['max_revenue_rm'])      ? $rules['max_revenue_rm']       : 'N/A';

// 3. Fetch Candidates (Startups)
$startups_query = "SELECT * FROM entities WHERE entity_type = 'startup'";
if (!empty($industry_focus)) {
    $inds        = [];
    $focus_count = count($industry_focus);
    for ($i = 0; $i < $focus_count; $i++) {
        $inds[] = "'" . mysqli_real_escape_string($conn, $industry_focus[$i]) . "'";
    }
    $startups_query .= " AND industry IN (" . implode(',', $inds) . ")";
}
$startups_res = mysqli_query($conn, $startups_query);

// Fallback to all startups if filtering yields empty result
if (mysqli_num_rows($startups_res) === 0) {
    $startups_res = mysqli_query($conn, "SELECT * FROM entities WHERE entity_type = 'startup'");
}

$startups = [];
while ($s = mysqli_fetch_assoc($startups_res)) {
    $s['profile_json'] = json_decode($s['profile_json'], true);
    $startups[] = $s;
}

// Build startup summary metrics for the system prompt
$startup_count      = count($startups);
$startup_industries = [];
$si_count           = count($startups);
for ($i = 0; $i < $si_count; $i++) {
    $ind = isset($startups[$i]['industry']) ? $startups[$i]['industry'] : '';
    if ($ind && !in_array($ind, $startup_industries)) {
        $startup_industries[] = $ind;
    }
}

// 4. Fetch Candidates (Mentors) & Historical Signals
$mentors_query = "SELECT * FROM entities WHERE entity_type = 'mentor'";
if (!empty($industry_focus)) {
    $inds        = [];
    $focus_count = count($industry_focus);
    for ($i = 0; $i < $focus_count; $i++) {
        $inds[] = "'" . mysqli_real_escape_string($conn, $industry_focus[$i]) . "'";
    }
    $mentors_query .= " AND industry IN (" . implode(',', $inds) . ")";
}
$mentors_res = mysqli_query($conn, $mentors_query);

// Fallback to all mentors if filtering yields empty result
if (mysqli_num_rows($mentors_res) === 0) {
    $mentors_res = mysqli_query($conn, "SELECT * FROM entities WHERE entity_type = 'mentor'");
}

$mentors = [];
while ($m = mysqli_fetch_assoc($mentors_res)) {
    $m['profile_json'] = json_decode($m['profile_json'], true);
    $m['expertise']    = json_decode($m['expertise'], true);
    if (!is_array($m['expertise'])) {
        $m['expertise'] = [];
    }

    // Build Historical Signals for the Learning Loop
    $m_id  = $m['id'];
    $out_q = "SELECT rating, outcome_notes, signal_tags_json
               FROM relationship_outcomes ro
               JOIN relationships r ON ro.relationship_id = r.id
               WHERE r.entity_a_id = $m_id";
    $out_res = mysqli_query($conn, $out_q);
    $history = [];
    while ($o = mysqli_fetch_assoc($out_res)) {
        $o['signal_tags_json'] = json_decode($o['signal_tags_json'], true);
        $history[] = $o;
    }
    $m['historical_outcomes'] = $history;
    $mentors[] = $m;
}

// Build mentor summary metrics for the system prompt
$mentor_count    = count($mentors);
$verified_count  = 0;
$mc              = count($mentors);
for ($i = 0; $i < $mc; $i++) {
    if (isset($mentors[$i]['verified']) && $mentors[$i]['verified']) {
        $verified_count++;
    }
}

// 5. Fetch Partners & Service Providers for context enrichment
$partners_query  = "SELECT id, name, industry, bio, profile_json FROM entities WHERE entity_type IN ('partner','service_provider')";
$partners_res    = mysqli_query($conn, $partners_query);
$ecosystem_context = [];
while ($p = mysqli_fetch_assoc($partners_res)) {
    $p['profile_json'] = json_decode($p['profile_json'], true);
    $ecosystem_context[] = $p;
}
$partner_count = count($ecosystem_context);

// -----------------------------------------------------------------------
// WIRE UP REAL SYSTEM PROMPT using live DB metrics
// This replaces any placeholder / haiku text.
// -----------------------------------------------------------------------
$industry_focus_str  = !empty($industry_focus) ? implode(', ', $industry_focus) : 'All Industries';
$startup_ind_str     = !empty($startup_industries) ? implode(', ', $startup_industries) : 'Various';

$prompt = "You are the EcosystemOS AI Matching Engine for the programme: \"{$programme_name}\" (Template: {$template_name}, Location: {$programme_location}).\n"
        . "Active Rule Set: Industry Focus = [{$industry_focus_str}] | Stage Focus = {$programme_stage} | Confidence Threshold = {$conf_threshold}% | Max Company Age = {$max_company_age} yrs | Max Revenue = RM {$max_revenue}.\n"
        . "Cohort Composition: {$startup_count} startups across [{$startup_ind_str}] industries | {$mentor_count} mentors available ({$verified_count} verified) | {$partner_count} ecosystem partners in context.\n"
        . "Task: Score each mentor-startup pair (0-100) using weighted factors (Industry Fit, Stage Fit, Expertise Gap Fit, Geography, Past Performance). "
        . "Provide clear reasoning, identify risk flags, and flag if human review is needed. "
        . "Return ONLY valid JSON exactly matching this format, with no markdown or extra text:\n"
        . "{ \"match_score\": 0, \"match_reasoning\": \"\", \"risk_flags\": [], \"needs_human_review\": false }";

// 6. Build Cohort Pairs using indexed loop (no foreach on $startups/$mentors)
$pairs        = [];
$s_total      = count($startups);
$m_total      = count($mentors);

for ($si = 0; $si < $s_total; $si++) {
    for ($mi = 0; $mi < $m_total; $mi++) {
        $pairs[] = ['startup' => $startups[$si], 'mentor' => $mentors[$mi]];
    }
}

// Optional Mock Fallback if API key missing (prevents total system failure during dev)
$mock_ai = empty($api_key);

// Limit pairs in real AI mode to avoid rate limits and request timeouts (max 8 pairs)
if (!$mock_ai && count($pairs) > 8) {
    $pairs = array_slice($pairs, 0, 8);
}
$results = [];
$errors  = [];

// Gemini AI Request Function with Exponential Backoff
function callGemini($system_prompt, $prompt_json, $accessToken) {
    $projectId = 'gen-lang-client-0858682595';
    $region    = 'us-central1';
    $model     = 'gemini-2.5-flash';
    $url       = "https://{$region}-aiplatform.googleapis.com/v1/projects/{$projectId}/locations/{$region}/publishers/google/models/{$model}:generateContent";

    // Authenticate using Bearer token header instead of URL parameter
    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer " . $accessToken
    ];

    $body = json_encode([
        "contents" => [
            [
                "role"  => "user",
                "parts" => [
                    [
                        "text" => $system_prompt . "\n" . $prompt_json
                    ]
                ]
            ]
        ],
        "generationConfig" => [
            "responseMimeType" => "application/json"
        ]
    ]);

    $max_retries = 3;
    $attempt     = 0;
    $last_error  = '';

    while ($attempt < $max_retries) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // IMPORTANT for Windows local environments using HTTPS:
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);

        $response   = curl_exec($ch);
        $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($http_code == 200) {
            $resp_data = json_decode($response, true);
            // Strict check of response fields using isset() - no null coalescing
            $candidate_text = isset($resp_data['candidates'][0]['content']['parts'][0]['text'])
                ? $resp_data['candidates'][0]['content']['parts'][0]['text']
                : null;
            if ($candidate_text !== null) {
                return $candidate_text;
            }
        } else {
            $last_error = "HTTP $http_code. Resp: " . substr($response, 0, 100) . " cURL: $curl_error";

            // Fail fast for fatal client/auth/not-found errors (400, 403, 404) or curl networking errors
            $is_fatal_error = ($curl_error || ($http_code != 429 && $http_code != 503)) ? true : false;
            if ($is_fatal_error) {
                return 'ERROR_FATAL: ' . $last_error;
            }
        }

        // Exponential backoff: 500ms, 1000ms, 2000ms...
        $attempt++;
        usleep(pow(2, $attempt) * 500000);
    }
    return 'ERROR_HTTP: ' . $last_error;
}

// 7. Batch Processing with Rate Limit Delays
$delay_ms  = 800; // 800ms delay between API requests
$p_total   = count($pairs);

for ($index = 0; $index < $p_total; $index++) {
    $startup = $pairs[$index]['startup'];
    $mentor  = $pairs[$index]['mentor'];

    if ($mock_ai) {
        // Fallback logic
        $score      = rand(50, 98);
        $risk_flags = [];
        if ($score < $conf_threshold) {
            $risk_flags[] = "low_confidence";
        }
        if (!$mentor['verified']) {
            $risk_flags[] = "mentor_unverified";
        }
        $needs_review = ($score < $conf_threshold || !empty($risk_flags));

        $ai_json = json_encode([
            'match_score'      => $score,
            'match_reasoning'  => "Match generated based on rules.",
            'risk_flags'       => $risk_flags,
            'needs_human_review' => $needs_review
        ]);
    } else {
        // Full Gemini Prompt Payload — dynamically built using real startup/mentor data
        $prompt_payload = json_encode([
            'task'             => 'Score this specific mentor-startup pair as instructed.',
            'programme_rules'  => $rules,
            'candidate_startup' => [
                'id'               => $startup['id'],
                'name'             => $startup['name'],
                'industry'         => $startup['industry'],
                'stage'            => $startup['stage'],
                'company_age_years' => $startup['company_age_years'],
                'revenue_rm'       => $startup['revenue_rm'],
                'team_size'        => $startup['team_size'],
                'location'         => $startup['location'],
                'profile_json'     => $startup['profile_json']
            ],
            'candidate_mentor' => [
                'id'       => $mentor['id'],
                'name'     => $mentor['name'],
                'industry' => $mentor['industry'],
                'expertise' => $mentor['expertise'],
                'location' => $mentor['location'],
                'verified' => $mentor['verified']
            ],
            'historical_signals' => $mentor['historical_outcomes'],
            'ecosystem_partners' => array_slice($ecosystem_context, 0, 5)
        ]);

        // Pass the wired $prompt as the system context to Gemini
        $ai_response = callGemini($prompt, $prompt_payload, $api_key);

        if ($ai_response && strpos($ai_response, 'ERROR_FATAL:') !== false) {
            $err_msg = str_replace('ERROR_FATAL: ', '', $ai_response);
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 'error',
                'message' => 'Fatal Gemini API Error: ' . $err_msg
            ]);
            exit;
        }
        if ($ai_response && strpos($ai_response, 'ERROR_HTTP:') !== false) {
            $err_msg = str_replace('ERROR_HTTP: ', '', $ai_response);
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gemini API connection error: ' . $err_msg
            ]);
            exit;
        }
        if ($ai_response) {
            $ai_json = $ai_response;
        } else {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 'error',
                'message' => 'Failed to generate match: API Response is empty.'
            ]);
            exit;
        }

        // Respect Rate Limits
        usleep($delay_ms * 1000);
    }

    // Parse Gemini Response
    $ai_data = json_decode($ai_json, true);
    if (!$ai_data) {
        $errors[] = "Failed to parse JSON for {$mentor['name']} vs {$startup['name']}";
        continue;
    }

    // 8. Validate Match Score
    $score = (float)(isset($ai_data['match_score']) ? $ai_data['match_score'] : 0);
    if ($score < 0 || $score > 100) {
        $errors[] = "Invalid score $score returned by AI for {$mentor['name']} vs {$startup['name']}";
        continue;
    }

    $reasoning       = mysqli_real_escape_string($conn, isset($ai_data['match_reasoning']) ? $ai_data['match_reasoning'] : '');
    $risk_flags_arr  = isset($ai_data['risk_flags']) ? $ai_data['risk_flags'] : [];
    $risk_flags_json = mysqli_real_escape_string($conn, json_encode($risk_flags_arr));

    // 9. Human Review Rules application
    $needs_human_review = 0;
    if (isset($ai_data['needs_human_review']) && $ai_data['needs_human_review']) {
        $needs_human_review = 1;
    }

    // Strict enforcement overrides AI if it misses a rule
    if ($score < $conf_threshold || count($risk_flags_arr) > 0 || !$mentor['verified']) {
        $needs_human_review = 1;
    }

    // 10. Update Relationships Table
    $prog_id = $programme_id;
    $s_id    = $startup['id'];
    $m_id    = $mentor['id'];

    $ins_query = "INSERT INTO relationships (programme_id, entity_a_id, entity_b_id, relationship_type, match_score, match_reasoning, risk_flags_json, needs_human_review, status)
                  VALUES ($prog_id, $m_id, $s_id, 'mentor_startup', $score, '$reasoning', '$risk_flags_json', $needs_human_review, 'proposed')
                  ON DUPLICATE KEY UPDATE
                  match_score=VALUES(match_score),
                  match_reasoning=VALUES(match_reasoning),
                  risk_flags_json=VALUES(risk_flags_json),
                  needs_human_review=VALUES(needs_human_review),
                  status=IF(status='proposed', 'proposed', status)";

    if (mysqli_query($conn, $ins_query)) {
        $results[] = [
            "mentor"  => $mentor['name'],
            "startup" => $startup['name'],
            "score"   => $score
        ];
    } else {
        $errors[] = mysqli_error($conn);
    }
}

// -----------------------------------------------------------------------
// ENFORCE VALID JSON OUTPUT
// Discard any buffered stray output (warnings, notices, BOM, whitespace)
// then output a strict JSON object the frontend AJAX handler expects.
// -----------------------------------------------------------------------
$text = "AI Match Generation Complete. Processed " . count($results) . " pairs"
      . (count($errors) > 0 ? " with " . count($errors) . " error(s)." : ".");

ob_end_clean();

$outputResponse = [
    'success'          => true,
    'match_result'     => $text,
    // Legacy keys kept so existing JS handler (data.status / data.matches_processed) also works
    'status'           => 'success',
    'message'          => 'AI Match Generation Complete',
    'matches_processed' => count($results),
    'errors'           => $errors,
    'mock_mode'        => $mock_ai
];

header('Content-Type: application/json');
echo json_encode($outputResponse);
exit;
?>
