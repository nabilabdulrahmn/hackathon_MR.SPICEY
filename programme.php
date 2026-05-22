<?php
include 'db.php';
$program_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Programme Detail — EcosystemOS</title>
<link rel="stylesheet" href="css/styles.css">
<style>
.breadcrumb{font-size:.75rem;color:var(--text-muted);margin-bottom:8px}
.breadcrumb a{color:var(--text-muted)}
.breadcrumb a:hover{color:var(--accent)}
.page-title-row{display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px}
.page-title-row h1{display:flex;align-items:center;gap:10px}
.title-actions{display:flex;gap:8px}
.btn-danger{background:transparent;color:var(--score-low);border:1px solid rgba(239,68,68,.3)}
.btn-danger:hover{background:var(--score-low-bg);border-color:var(--score-low)}
.three-col{display:grid;grid-template-columns:260px 1fr 280px;gap:20px;margin-top:24px}
.info-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:.8125rem}
.info-row:last-child{border-bottom:none}
.info-label{color:var(--text-muted)}
.info-value{color:var(--text-primary);font-weight:500;text-align:right}
.rule-item{font-size:.8125rem;color:var(--text-secondary);padding:4px 0;display:flex;align-items:center;gap:6px}
.rule-check{color:var(--score-high);font-size:.75rem}
.mini-stats{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.mini-stat{text-align:center;padding:10px 6px;background:var(--bg-surface);border-radius:6px}
.mini-stat .num{font-size:1.25rem;font-weight:800;font-variant-numeric:tabular-nums}
.mini-stat .lbl{font-size:.625rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em;margin-top:2px}
.match-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px}
.match-header select{background:var(--bg-input);border:1px solid var(--border);color:var(--text-primary);padding:6px 10px;border-radius:var(--r-input);font-family:inherit;font-size:.8125rem}
.btn-generate{display:inline-flex;align-items:center;gap:6px}
.match-card{border:1px solid var(--border);border-radius:var(--r-card);padding:16px;margin-bottom:12px;background:var(--bg-card);transition:all .2s var(--ease)}
.match-card:hover{border-color:var(--border-hover);box-shadow:var(--shadow-sm)}
.match-card.needs-review{border-color:rgba(245,158,11,.3);background:linear-gradient(135deg,var(--bg-card),rgba(245,158,11,.03))}
.match-card.ineligible{border-color:rgba(82,82,91,.4);opacity:.75}
.mc-top{display:flex;justify-content:space-between;align-items:flex-start}
.mc-identity{display:flex;align-items:center;gap:10px}
.mc-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.6875rem;font-weight:700;color:#fff;flex-shrink:0}
.mc-name{font-weight:600;font-size:.875rem}
.mc-role{font-size:.6875rem;padding:2px 8px;border-radius:var(--r-pill);background:var(--accent-muted);color:var(--accent-hover)}
.mc-score{font-size:1.5rem;font-weight:800;font-variant-numeric:tabular-nums;padding:4px 14px;border-radius:var(--r-pill);min-width:56px;text-align:center}
.mc-tags{display:flex;gap:6px;flex-wrap:wrap;margin:10px 0}
.mc-tag{font-size:.6875rem;padding:2px 8px;border-radius:var(--r-pill);background:var(--bg-surface);color:var(--text-muted);border:1px solid var(--border)}
.mc-reasoning{font-size:.8125rem;color:var(--text-secondary);line-height:1.6;margin:8px 0}
.mc-signals{display:flex;gap:12px;flex-wrap:wrap;margin:8px 0;padding:10px;background:var(--bg-surface);border-radius:6px;font-size:.75rem}
.mc-signal{display:flex;flex-direction:column;align-items:center;gap:2px}
.mc-signal .s-val{font-weight:700;font-variant-numeric:tabular-nums;color:var(--text-primary)}
.mc-signal .s-lbl{color:var(--text-muted);font-size:.625rem}
.mc-risks{display:flex;gap:6px;flex-wrap:wrap;margin:6px 0}
.mc-risk{font-size:.6875rem;padding:2px 8px;border-radius:var(--r-pill);font-weight:600}
.mc-risk-green{background:var(--score-high-bg);color:var(--score-high)}
.mc-risk-amber{background:var(--score-mid-bg);color:var(--score-mid)}
.mc-risk-red{background:var(--score-low-bg);color:var(--score-low)}
.mc-actions{display:flex;gap:6px;margin-top:10px;align-items:center}
.btn-approve{background:rgba(34,197,94,.15);color:var(--score-high);border:1px solid rgba(34,197,94,.3);font-size:.75rem;padding:5px 14px;border-radius:var(--r-btn);cursor:pointer;font-family:inherit;font-weight:600;transition:all .15s}
.btn-approve:hover{background:rgba(34,197,94,.25)}
.btn-reject{background:transparent;color:var(--text-muted);border:1px solid var(--border);font-size:.75rem;padding:5px 14px;border-radius:var(--r-btn);cursor:pointer;font-family:inherit;transition:all .15s}
.btn-reject:hover{border-color:var(--score-low);color:var(--score-low)}
.btn-review{background:rgba(245,158,11,.15);color:var(--score-mid);border:1px solid rgba(245,158,11,.3);font-size:.75rem;padding:5px 14px;border-radius:var(--r-btn);cursor:pointer;font-family:inherit;font-weight:600;transition:all .15s}
.btn-override{background:transparent;color:var(--text-muted);border:1px solid var(--border);font-size:.6875rem;padding:4px 10px;border-radius:var(--r-btn);cursor:pointer;font-family:inherit}
.mc-banner{font-size:.75rem;padding:6px 12px;border-radius:6px;margin-bottom:10px;font-weight:500}
.mc-banner-amber{background:var(--score-mid-bg);color:var(--score-mid)}
.mc-banner-red{background:var(--score-low-bg);color:var(--score-low)}
.rel-card{padding:12px;border:1px solid var(--border);border-radius:var(--r-card);margin-bottom:8px;background:var(--bg-card)}
.rel-parties{font-size:.8125rem;font-weight:600}
.rel-meta{font-size:.6875rem;color:var(--text-muted);margin-top:4px}
.rel-progress{height:4px;background:var(--border);border-radius:2px;margin-top:8px;overflow:hidden}
.rel-progress-fill{height:100%;background:var(--accent);border-radius:2px;transition:width .5s}
.section-count{background:var(--accent-muted);color:var(--accent);font-size:.6875rem;font-weight:700;padding:2px 8px;border-radius:var(--r-pill);margin-left:8px}
.empty-state{text-align:center;padding:24px;color:var(--text-muted);font-size:.8125rem;line-height:1.6}
.past-outcome{padding:12px;border:1px solid var(--border);border-radius:var(--r-card);margin-bottom:8px}
.past-outcome .po-parties{font-size:.8125rem;font-weight:600}
.past-outcome .po-cohort{font-size:.6875rem;color:var(--text-muted)}
.past-outcome .po-rating{color:var(--score-mid);font-size:.75rem;margin-top:4px}
.past-outcome .po-note{font-size:.75rem;color:var(--text-secondary);margin-top:4px;font-style:italic}
.expand-btn{font-size:.75rem;color:var(--accent);cursor:pointer;background:none;border:none;font-family:inherit;padding:0}
.expand-btn:hover{text-decoration:underline}
.hidden{display:none}
@media(max-width:1100px){.three-col{grid-template-columns:1fr;}}
</style>
</head>
<body>
<nav class="topnav">
  <a href="dashboard.html" class="topnav-logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><circle cx="12" cy="7" r="3" fill="currentColor" stroke="none" opacity=".9"/><circle cx="5" cy="18" r="3" fill="currentColor" stroke="none" opacity=".9"/><circle cx="19" cy="18" r="3" fill="currentColor" stroke="none" opacity=".9"/><line x1="12" y1="10" x2="5" y2="15" opacity=".5"/><line x1="12" y1="10" x2="19" y2="15" opacity=".5"/><line x1="5" y1="18" x2="19" y2="18" opacity=".5"/></svg>EcosystemOS</a>
  <div class="topnav-links"><a href="dashboard.html">Dashboard</a><a href="templates.html">Templates</a><a href="programme.php" class="active">Programmes</a><a href="entities.html">Entities</a><a href="insights.html">Insights</a></div>
  <div class="topnav-right">
    <span class="topnav-org">Organisation: Cradle Fund <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></span>
    <div class="topnav-bell"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg><span class="dot"></span></div>
    <div class="topnav-avatar" onclick="this.querySelector('.avatar-dropdown').classList.toggle('open')">FH<div class="avatar-dropdown"><a href="#">Profile</a><a href="#">Settings</a><a href="index.html">Sign out</a></div></div>
  </div>
</nav>

<div class="page">
  <!-- ═══ DETAIL VIEW ═══ -->
  <div id="detailView" class="hidden">
    <div class="breadcrumb"><a href="dashboard.html">Dashboard</a> › <a href="programme.php">Programmes</a> › <span id="breadcrumbProgName">MYStartup Pre-Accelerator Cohort 6</span></div>
    <div class="page-title-row">
      <h1 id="detailTitle">MYStartup Pre-Accelerator Cohort 6 — KL FinTech <span class="pill pill-active">Active</span></h1>
      <div class="title-actions"><button class="btn btn-secondary" id="editProgBtn">Edit Programme</button><button class="btn btn-danger" id="endProgBtn">End Programme</button></div>
    </div>

    <div class="three-col">
      <!-- ═══ LEFT COLUMN ═══ -->
      <div>
        <div class="card" style="margin-bottom:12px">
          <h3 style="margin-bottom:12px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:4px"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg>Programme Details</h3>
          <div class="info-row"><span class="info-label">Template</span><span class="info-value" id="detailTemplateVal">Pre-Accelerator Standard</span></div>
          <div class="info-row"><span class="info-label">Location</span><span class="info-value" id="detailLocVal">Kuala Lumpur</span></div>
          <div class="info-row"><span class="info-label">Started</span><span class="info-value" id="detailStartVal">12 May 2026</span></div>
          <div class="info-row"><span class="info-label">Ends</span><span class="info-value" id="detailEndVal">12 September 2026</span></div>
          <div class="info-row"><span class="info-label">Programme Lead</span><span class="info-value">Faiz Hassan</span></div>
          <div class="info-row"><span class="info-label">Cohort Size</span><span class="info-value">18 startups · 12 mentors</span></div>
        </div>

        <div class="card" style="margin-bottom:12px">
          <h3 style="margin-bottom:12px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:4px"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>Active Rule Set</h3>
          <div class="rule-item"><span class="rule-check">●</span> 1 Lead + 2 Support Mentors per startup</div>
          <div class="rule-item"><span class="rule-check">●</span> Duration: 4 months</div>
          <div class="rule-item" style="margin:6px 0">Industry Focus:</div>
          <div style="display:flex;gap:4px;flex-wrap:wrap;margin-bottom:8px"><span class="chip">FinTech</span><span class="chip">HealthTech</span><span class="chip">EdTech</span></div>
          <div style="font-size:.75rem;color:var(--text-muted);margin-top:8px;border-top:1px solid var(--border);padding-top:8px">Eligibility:</div>
          <div class="rule-item"><span class="rule-check">✓</span> Max company age 5 years</div>
          <div class="rule-item"><span class="rule-check">✓</span> Max revenue RM 3M</div>
          <div class="rule-item"><span class="rule-check">✓</span> Min 51% Malaysian-owned</div>
          <div class="rule-item" style="margin-top:8px"><span style="color:var(--accent)">◎</span> Confidence threshold: <strong>70</strong></div>
        </div>

        // Fetch dynamic counts for Programme Stats widget via a single consolidated query
        $stats_query = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = 'proposed' THEN 1 ELSE 0 END) as awaiting,
            AVG(match_score) as avg_score 
        FROM relationships 
        WHERE programme_id = $program_id";
        
        $stats_result = mysqli_query($conn, $stats_query);
        $stats_row = mysqli_fetch_assoc($stats_result);
        
        $php_stat_proposed = isset($stats_row['total']) ? (int)$stats_row['total'] : 0;
        $php_stat_approved = isset($stats_row['approved']) && $stats_row['approved'] !== null ? (int)$stats_row['approved'] : 0;
        $php_stat_rejected = isset($stats_row['rejected']) && $stats_row['rejected'] !== null ? (int)$stats_row['rejected'] : 0;
        $php_stat_awaiting = isset($stats_row['awaiting']) && $stats_row['awaiting'] !== null ? (int)$stats_row['awaiting'] : 0;
        $php_stat_avg_score = isset($stats_row['avg_score']) && $stats_row['avg_score'] !== null ? (int)round($stats_row['avg_score']) : 0;

        $avg_color = 'var(--score-high)';
        if ($php_stat_avg_score < 65) {
            $avg_color = 'var(--score-low)';
        } else if ($php_stat_avg_score < 80) {
            $avg_color = 'var(--score-mid)';
        }
        ?>
        <div class="card">
          <h3 style="margin-bottom:12px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:4px"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>Programme Stats</h3>
          <div class="mini-stats">
            <div class="mini-stat"><div class="num" id="statProposed" style="color:var(--text-primary)"><?php echo $php_stat_proposed; ?></div><div class="lbl">Proposed</div></div>
            <div class="mini-stat"><div class="num" id="statApproved" style="color:var(--score-high)"><?php echo $php_stat_approved; ?></div><div class="lbl">Approved</div></div>
            <div class="mini-stat"><div class="num" id="statRejected" style="color:var(--score-low)"><?php echo $php_stat_rejected; ?></div><div class="lbl">Rejected</div></div>
            <div class="mini-stat"><div class="num" id="statAwaiting" style="color:var(--score-mid)"><?php echo $php_stat_awaiting; ?></div><div class="lbl">Awaiting</div></div>
          </div>
          <div style="text-align:center;margin-top:12px;padding-top:12px;border-top:1px solid var(--border)">
            <div class="label">Avg Score</div>
            <div id="statAvgScore" style="font-size:1.75rem;font-weight:800;color:<?php echo $avg_color; ?>;font-variant-numeric:tabular-nums"><?php echo $php_stat_avg_score > 0 ? $php_stat_avg_score : '—'; ?></div>
          </div>
        </div>
      </div>

      <!-- ═══ CENTRE COLUMN ═══ -->
      <div>
        <div class="match-header">
          <div style="display:flex;align-items:center;gap:8px">
            <h2>Proposed Matches for</h2>
            <select id="startupSelect">
              <option value="">All Startups</option>
            </select>
          </div>
          <button class="btn btn-primary btn-generate">✨ Generate Matches</button>
        </div>

        <div id="matchesContainer">
           <div style="padding:40px;text-align:center;color:var(--text-muted);background:var(--card-bg);border-radius:12px;border:1px dashed var(--border)">
              Loading matches...
           </div>
        </div>
      </div>

      <!-- ═══ RIGHT COLUMN ═══ -->
      <div>
        <?php
        $active_query = "SELECT r.*, e_mentor.name AS mentor_name, e_startup.name AS startup_name 
                         FROM relationships r
                         JOIN entities e_mentor ON r.entity_a_id = e_mentor.id
                         JOIN entities e_startup ON r.entity_b_id = e_startup.id
                         WHERE r.programme_id = $program_id AND r.status = 'active'";
        $active_result = mysqli_query($conn, $active_query);
        $active_count = 0;
        $active_html = '';
        while ($row = mysqli_fetch_assoc($active_result)) {
            $active_count++;
            
            $days_active = 0;
            $started_text = 'Not started';
            if ($row['activated_at']) {
                $activated_time = strtotime($row['activated_at']);
                if ($activated_time) {
                    $days_active = max(1, round((time() - $activated_time) / 86400));
                    $started_text = 'Started ' . date('d M Y', $activated_time);
                }
            }
            
            $progress_width = min(100, max(2, $days_active * 2));
            
            $active_html .= '<div class="rel-card">';
            $active_html .= '<div class="rel-parties">' . htmlspecialchars($row['mentor_name']) . ' ↔ ' . htmlspecialchars($row['startup_name']) . '</div>';
            $active_html .= '<div class="rel-meta">' . htmlspecialchars($started_text) . ' · ' . $days_active . ' days active</div>';
            $active_html .= '<div class="rel-progress"><div class="rel-progress-fill" style="width:' . $progress_width . '%"></div></div>';
            $active_html .= '<button class="btn btn-ghost" style="font-size:.6875rem;margin-top:6px;padding:2px 8px">Conclude</button>';
            $active_html .= '</div>';
        }
        ?>
        <h3 style="margin-bottom:12px">Active Relationships <span class="section-count" id="activeRelsCount"><?php echo $active_count; ?></span></h3>
        <div id="activeRelsContainer">
            <?php echo $active_html ? $active_html : '<div class="card empty-state">No active relationships yet.</div>'; ?>
        </div>

        <?php
        $concluded_query = "SELECT r.*, e_mentor.name AS mentor_name, e_startup.name AS startup_name 
                            FROM relationships r
                            JOIN entities e_mentor ON r.entity_a_id = e_mentor.id
                            JOIN entities e_startup ON r.entity_b_id = e_startup.id
                            WHERE r.programme_id = $program_id AND r.status = 'concluded'";
        $concluded_result = mysqli_query($conn, $concluded_query);
        $concluded_count = 0;
        $concluded_html = '';
        while ($row = mysqli_fetch_assoc($concluded_result)) {
            $concluded_count++;
            $concluded_html .= '<div class="rel-card" style="opacity:0.8">';
            $concluded_html .= '<div class="rel-parties">' . htmlspecialchars($row['mentor_name']) . ' ↔ ' . htmlspecialchars($row['startup_name']) . '</div>';
            $concluded_html .= '<div class="rel-meta">Concluded</div>';
            $concluded_html .= '</div>';
        }
        ?>
        <h3 style="margin:24px 0 12px">Concluded (this cohort) <span class="section-count" id="concludedRelsCount"><?php echo $concluded_count; ?></span></h3>
        <div id="concludedRelsContainer">
            <?php 
            if ($concluded_html) {
                echo $concluded_html;
            } else {
                echo '<div class="card empty-state"><div style="font-size:1.5rem;margin-bottom:8px;opacity:.3">📋</div>No concluded relationships yet.<br>When you wrap up a mentorship, capture the outcome here to feed the learning loop.</div>';
            }
            ?>
        </div>

        <h3 style="margin:24px 0 12px">Recent Concluded — All Programmes</h3>
        <div class="past-outcome"><div class="po-parties">Daniel Lee ↔ HealthHero</div><div class="po-cohort">Cohort 5 · Concluded Dec 2025</div><div class="po-rating">★★★★★</div><div class="po-note">"Daniel's network unlocked Series A within 9 months. Exceptional operator-to-founder chemistry."</div></div>
        <div class="past-outcome"><div class="po-parties">Aisha Rahman ↔ EduFlow</div><div class="po-cohort">Cohort 5 · Concluded Dec 2025</div><div class="po-rating">★★★★☆</div><div class="po-note">"Excellent on go-to-market strategy, weaker on hiring advice. Strong overall outcome."</div></div>
      </div>
    </div>
  </div>

  <!-- ═══ LIST VIEW ═══ -->
  <div id="listView" class="hidden">
    <div class="breadcrumb"><a href="dashboard.html">Dashboard</a> › Programmes</div>
    <div class="page-title-row" style="margin-bottom: 24px;">
      <h1>Programmes</h1>
      <div class="title-actions">
        <button class="btn btn-primary" id="btnLaunchProgList">+ Launch Programme</button>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar" style="margin-bottom: 24px;">
      <input type="text" class="input" id="searchProgrammes" placeholder="Search programmes..." style="max-width: 260px;">
      <button class="filter-chip active" data-status="all">All</button>
      <button class="filter-chip" data-status="active">Active</button>
      <button class="filter-chip" data-status="pending">Pending</button>
      <button class="filter-chip" data-status="completed">Completed</button>
    </div>

    <!-- Grid View of Programmes -->
    <div class="grid-4" id="programmesListGrid" style="margin-top: 16px;">
      <div style="grid-column: span 4; text-align: center; padding: 40px; color: var(--text-muted);">
        Loading programmes...
      </div>
    </div>
  </div>
</div>
<!-- Mentor Profile Modal -->
<div id="mentorModal" class="modal-overlay" style="display:none">
  <div class="card" style="width:520px;max-width:90vw;max-height:85vh;overflow-y:auto;position:relative">
    <button onclick="document.getElementById('mentorModal').style.display='none'" style="position:absolute;top:12px;right:12px;background:none;border:none;color:var(--text-muted);font-size:1.25rem;cursor:pointer">&times;</button>
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px"><div class="mc-avatar" style="background:#6366f1;width:48px;height:48px;font-size:.875rem" id="mAvatar">AR</div><div><h2 id="mName">Aisha Rahman</h2><span class="mc-role" id="mRole">Lead Mentor candidate</span></div></div>
    <div id="mBody">
      <div class="info-row"><span class="info-label">Industry</span><span class="info-value">FinTech</span></div>
      <div class="info-row"><span class="info-label">Expertise</span><span class="info-value">Series A operator</span></div>
      <div class="info-row"><span class="info-label">Location</span><span class="info-value">Kuala Lumpur</span></div>
      <div class="info-row"><span class="info-label">Cohorts Mentored</span><span class="info-value">5</span></div>
      <div class="info-row"><span class="info-label">Avg Mentee Rating</span><span class="info-value" style="color:var(--score-mid)">★★★★★ (5.0)</span></div>
      <div class="info-row"><span class="info-label">Verified</span><span class="info-value" style="color:var(--score-high)">✓ Verified</span></div>
    </div>
    <div style="margin-top:16px;display:flex;gap:8px">
      <button class="btn btn-primary" onclick="document.getElementById('mentorModal').style.display='none'">Close</button>
      <a href="entities.html" class="btn btn-secondary" style="text-decoration:none">View in Entity Library</a>
    </div>
  </div>
</div>

<!-- Edit Programme Modal -->
<div id="editModal" class="modal-overlay" style="display:none">
  <div class="card" style="width:480px;max-width:90vw;position:relative">
    <button onclick="document.getElementById('editModal').style.display='none'" style="position:absolute;top:12px;right:12px;background:none;border:none;color:var(--text-muted);font-size:1.25rem;cursor:pointer">&times;</button>
    <h2 style="margin-bottom:16px">Edit Programme</h2>
    <div class="form-group"><label class="form-label">Programme Name</label><input class="input" id="editName" value="MYStartup Pre-Accelerator Cohort 6 — KL FinTech"></div>
    <div class="form-group"><label class="form-label">Location</label><input class="input" id="editLoc" value="Kuala Lumpur"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="form-group"><label class="form-label">Start Date</label><input class="input" type="date" value="2026-05-12"></div>
      <div class="form-group"><label class="form-label">End Date</label><input class="input" type="date" value="2026-09-12"></div>
    </div>
    <div style="display:flex;gap:8px;margin-top:16px">
      <button class="btn btn-primary" id="saveProgrammeBtn">Save Changes</button>
      <button class="btn btn-secondary" onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
    </div>
  </div>
</div>

<!-- End Programme Modal -->
<div id="endModal" class="modal-overlay" style="display:none">
  <div class="card" style="width:440px;max-width:90vw;position:relative">
    <button onclick="document.getElementById('endModal').style.display='none'" style="position:absolute;top:12px;right:12px;background:none;border:none;color:var(--text-muted);font-size:1.25rem;cursor:pointer">&times;</button>
    <h2 style="margin-bottom:8px;color:var(--score-low)">⚠ End Programme</h2>
    <p style="font-size:.875rem;color:var(--text-secondary);margin-bottom:16px">This will mark the programme as <strong>Completed</strong> and conclude all active relationships. This action cannot be undone.</p>
    <div class="form-group"><label class="form-label">Reason for ending</label><textarea class="textarea" placeholder="e.g. Programme duration completed, all milestones achieved..."></textarea></div>
    <div style="display:flex;gap:8px;margin-top:12px">
      <button class="btn btn-danger" style="background:var(--score-low);color:#fff;border-color:var(--score-low)" id="confirmEndProgBtn">Confirm End Programme</button>
      <button class="btn btn-secondary" onclick="document.getElementById('endModal').style.display='none'">Cancel</button>
    </div>
  </div>
</div>

<!-- Launch Programme Modal -->
<div id="launchModal" class="modal-overlay" style="display:none">
  <div class="card" style="width:480px;max-width:90vw;position:relative">
    <button onclick="document.getElementById('launchModal').style.display='none'" style="position:absolute;top:12px;right:12px;background:none;border:none;color:var(--text-muted);font-size:1.25rem;cursor:pointer">&times;</button>
    <h2 style="margin-bottom:16px">Launch New Programme</h2>
    <div class="form-group"><label class="form-label">Programme Name</label><input class="input" id="launchName" placeholder="e.g. MYStartup Cohort 7"></div>
    <div class="form-group"><label class="form-label">Linkage Template</label><select class="input" id="launchTemplate"><option value="">Loading templates...</option></select></div>
    <div class="form-group"><label class="form-label">Location</label><input class="input" id="launchLoc" placeholder="e.g. Kuala Lumpur"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="form-group"><label class="form-label">Start Date</label><input class="input" type="date" id="launchStart"></div>
      <div class="form-group"><label class="form-label">End Date</label><input class="input" type="date" id="launchEnd"></div>
    </div>
    <div style="display:flex;gap:8px;margin-top:16px">
      <button class="btn btn-primary" id="saveLaunchBtn">Launch Programme</button>
      <button class="btn btn-secondary" onclick="document.getElementById('launchModal').style.display='none'">Cancel</button>
    </div>
  </div>
</div>

<script>
// ═══ Mentor Profile Data ═══
var mentorProfiles = {
  'Aisha Rahman': {industry:'FinTech',expertise:'Series A operator, Digital Payments',location:'Kuala Lumpur',cohorts:5,rating:'★★★★★ (5.0)',verified:true,color:'#6366f1'},
  'Daniel Lee': {industry:'FinTech',expertise:'Ex-founder FinPay, SPARK winner',location:'Kuala Lumpur',cohorts:6,rating:'★★★★★ (4.7)',verified:true,color:'#22c55e'},
  'Sarah Tan': {industry:'EdTech',expertise:'Cross-industry mentor, Fundraising',location:'Kuala Lumpur',cohorts:3,rating:'★★★★☆ (4.2)',verified:true,color:'#f59e0b'},
  'James Wong': {industry:'FinTech',expertise:'New mentor (unverified)',location:'Johor Bahru',cohorts:0,rating:'No ratings yet',verified:false,color:'#71717a'},
  'Marcus Chen': {industry:'FinTech',expertise:'Competing startup (COI)',location:'Kuala Lumpur',cohorts:2,rating:'★★★☆☆ (3.5)',verified:true,color:'#3f3f46'},
  'Nurul Huda Ibrahim': {industry:'HealthTech',expertise:'Telemedicine, Hospital Systems',location:'Kuala Lumpur',cohorts:4,rating:'★★★★★ (4.8)',verified:true,color:'#6366f1'},
  'Dr. Lim Chee Keong': {industry:'HealthTech',expertise:'MedTech regulatory, FDA/MDA',location:'Penang',cohorts:3,rating:'★★★★☆ (4.3)',verified:true,color:'#22c55e'},
  'Siti Mariam Yusof': {industry:'HealthTech',expertise:'Mental health tech, UX',location:'Johor Bahru',cohorts:1,rating:'★★★★☆ (4.0)',verified:false,color:'#f59e0b'},
  'Ahmad Faizal Osman': {industry:'AgriTech',expertise:'Precision agriculture, IoT',location:'Sabah',cohorts:5,rating:'★★★★★ (4.9)',verified:true,color:'#6366f1'},
  'Datin Rosnah Abdullah': {industry:'AgriTech',expertise:'Aquaculture, Marine biology',location:'Sabah',cohorts:3,rating:'★★★★☆ (4.5)',verified:true,color:'#22c55e'},
  'Prof. Kamal Ariffin': {industry:'EdTech',expertise:'Curriculum design, TVET policy',location:'Kuala Lumpur',cohorts:4,rating:'★★★★★ (4.6)',verified:true,color:'#6366f1'},
  'Lee Jia Hao': {industry:'AgriTech',expertise:'Drone technology, GIS mapping',location:'Johor Bahru',cohorts:1,rating:'★★★☆☆ (3.0)',verified:false,color:'#71717a'}
};

// ═══ Match Data Per Startup ═══
var matchData = {
  'PaddyPay': null, // Already rendered in HTML
  'HealthHero': [
    {name:'Nurul Huda Ibrahim',initials:'NH',color:'#6366f1',role:'Lead Mentor candidate',score:89,tier:'score-high',tags:['HealthTech specialist','Telemedicine expert','KL-based'],reasoning:'Excellent industry fit — Nurul\'s 18 years in hospital systems and telemedicine directly map to HealthHero\'s patient portal mission. Prior Cohort 4 mentee CareSync reached seed.',risks:'<span class="mc-risk mc-risk-green">✓ No risks flagged</span>',type:'normal'},
    {name:'Dr. Lim Chee Keong',initials:'LC',color:'#22c55e',role:'Support Mentor candidate',score:82,tier:'score-high',tags:['MedTech regulatory','FDA/MDA approvals','Penang-based'],reasoning:'Strong regulatory compliance expertise. Dr. Lim can guide HealthHero through MDA device registration if they pivot to connected devices.',risks:'<span class="mc-risk mc-risk-amber">Geography: Penang vs KL startup</span>',type:'normal'},
    {name:'Siti Mariam Yusof',initials:'SM',color:'#f59e0b',role:'Support Mentor candidate',score:71,tier:'score-mid',tags:['Mental health tech','UX specialist','Limited HealthTech breadth'],reasoning:'UX for wellness apps transferable to patient portals. Limited hospital-system domain knowledge flagged.',risks:'<span class="mc-risk mc-risk-amber">Narrow domain expertise</span>',type:'normal'}
  ],
  'SabahHarvest': [
    {name:'Ahmad Faizal Osman',initials:'AF',color:'#6366f1',role:'Lead Mentor candidate',score:94,tier:'score-high',tags:['AgriTech pioneer','IoT sensors','Sabah-based'],reasoning:'Perfect geography and industry match. Ahmad\'s 14 years in precision agriculture and palm oil supply chain directly support SabahHarvest\'s pre-seed AgriTech mission.',risks:'<span class="mc-risk mc-risk-green">✓ No risks flagged</span>',type:'normal'},
    {name:'Datin Rosnah Abdullah',initials:'RA',color:'#22c55e',role:'Support Mentor candidate',score:88,tier:'score-high',tags:['Aquaculture expert','FAMA networks','Sabah-based'],reasoning:'Deep FAMA distribution knowledge crucial for SabahHarvest\'s market access. 22 years of marine biology and aquaculture expertise.',risks:'<span class="mc-risk mc-risk-green">✓ No risks flagged</span>',type:'normal'},
    {name:'Lee Jia Hao',initials:'LJ',color:'#71717a',role:'Support Mentor candidate',score:68,tier:'score-low',tags:['Drone technology','Limited mentoring'],reasoning:'Drone expertise relevant but only 1 prior mentorship. Score adjusted for data sparsity.',risks:'<span class="mc-risk mc-risk-amber">Data sparsity</span><span class="mc-risk mc-risk-amber">Unverified profile</span>',type:'review',banner:'⚠️ Needs Human Review — confidence below threshold (75)'}
  ],
  'EduFlow': [
    {name:'Prof. Kamal Ariffin',initials:'KA',color:'#6366f1',role:'Lead Mentor candidate',score:91,tier:'score-high',tags:['EdTech authority','TVET policy','KL-based'],reasoning:'Exceptional fit — Prof. Kamal\'s 25 years shaping national curriculum and TVET policy directly aligns with EduFlow\'s learning platform. 7 startups mentored with strong outcomes.',risks:'<span class="mc-risk mc-risk-green">✓ No risks flagged</span>',type:'normal'},
    {name:'Sarah Tan',initials:'ST',color:'#f59e0b',role:'Support Mentor candidate',score:84,tier:'score-high',tags:['EdTech veteran','Cross-industry','Fundraising specialist'],reasoning:'EdTech is Sarah\'s primary domain. Strong fundraising skills directly applicable to EduFlow\'s seed-stage needs.',risks:'<span class="mc-risk mc-risk-green">✓ No risks flagged</span>',type:'normal'},
    {name:'Daniel Lee',initials:'DL',color:'#22c55e',role:'Support Mentor candidate',score:72,tier:'score-mid',tags:['FinTech background','Transferable ops skills'],reasoning:'Cross-industry support — Daniel\'s operational scaling expertise is transferable though primary domain is FinTech not EdTech.',risks:'<span class="mc-risk mc-risk-amber">Cross-industry: limited EdTech track record</span>',type:'normal'}
  ]
};

// Old static match rendering removed — matches are now loaded dynamically via API
var matchContainer = document.getElementById('matchesContainer');


// ═══ Stats Tracking ═══
var stats = {
  proposed: <?php echo $php_stat_proposed; ?>,
  approved: <?php echo $php_stat_approved; ?>,
  rejected: <?php echo $php_stat_rejected; ?>,
  awaiting: <?php echo $php_stat_awaiting; ?>,
  scores: [<?php
    $js_scores_query = "SELECT match_score FROM relationships WHERE programme_id = $program_id AND match_score IS NOT NULL";
    $js_scores_res = mysqli_query($conn, $js_scores_query);
    $js_scores = [];
    while ($s_row = mysqli_fetch_assoc($js_scores_res)) {
        $js_scores[] = $s_row['match_score'];
    }
    echo implode(',', $js_scores);
  ?>]
};
function updateStats() {
  document.getElementById('statProposed').textContent = stats.proposed;
  document.getElementById('statApproved').textContent = stats.approved;
  document.getElementById('statRejected').textContent = stats.rejected;
  document.getElementById('statAwaiting').textContent = stats.awaiting;
  if (stats.scores.length > 0) {
    var avg = Math.round(stats.scores.reduce(function(a,b){return a+b},0) / stats.scores.length);
    var el = document.getElementById('statAvgScore');
    el.textContent = avg;
    el.style.color = avg >= 80 ? 'var(--score-high)' : avg >= 65 ? 'var(--score-mid)' : 'var(--score-low)';
  } else {
    var el = document.getElementById('statAvgScore');
    el.textContent = '—';
    el.style.color = 'var(--text-muted)';
  }
}

// ═══ Bind all card buttons ═══
function bindCardButtons() {
  // View Mentor Profile
  document.querySelectorAll('.view-mentor-link').forEach(function(a) {
    a.addEventListener('click', function(e) {
      e.preventDefault();
      var card = this.closest('.match-card');
      var name = card.querySelector('.mc-name').textContent;
      var p = mentorProfiles[name] || {industry:'—',expertise:'—',location:'—',cohorts:0,rating:'—',verified:false,color:'#71717a'};
      var initials = name.split(' ').map(function(w){return w[0]}).join('');
      document.getElementById('mName').textContent = name;
      document.getElementById('mAvatar').textContent = initials;
      document.getElementById('mAvatar').style.background = p.color;
      document.getElementById('mBody').innerHTML =
        '<div class="info-row"><span class="info-label">Industry</span><span class="info-value">' + p.industry + '</span></div>' +
        '<div class="info-row"><span class="info-label">Expertise</span><span class="info-value">' + p.expertise + '</span></div>' +
        '<div class="info-row"><span class="info-label">Location</span><span class="info-value">' + p.location + '</span></div>' +
        '<div class="info-row"><span class="info-label">Cohorts Mentored</span><span class="info-value">' + p.cohorts + '</span></div>' +
        '<div class="info-row"><span class="info-label">Avg Mentee Rating</span><span class="info-value" style="color:var(--score-mid)">' + p.rating + '</span></div>' +
        '<div class="info-row"><span class="info-label">Verified</span><span class="info-value" style="color:' + (p.verified ? 'var(--score-high)' : 'var(--score-mid)') + '">' + (p.verified ? '✓ Verified' : '⚠ Unverified') + '</span></div>';
      document.getElementById('mentorModal').style.display = 'flex';
    });
  });

  // Approve — updates stats and DB
  document.querySelectorAll('.btn-approve').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var name = this.closest('.match-card').querySelector('.mc-name').textContent;
      var score = parseInt(this.closest('.match-card').querySelector('.mc-score').textContent) || 0;
      var startup = document.getElementById('startupSelect').value;
      var self = this;
      
      fetch('api_relationships.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'approve', programme_id: progId, mentor_name: name, startup_name: startup, match_score: score })
      }).then(res => res.json()).then(data => {
        if (data.status === 'success') {
          self.closest('.match-card').style.borderColor = 'rgba(34,197,94,.5)';
          self.textContent = '✓ Approved'; self.disabled = true; self.style.opacity = '.6';
          stats.approved++; stats.awaiting = Math.max(0, stats.awaiting - 1);
          if (score > 0) stats.scores.push(score);
          updateStats();
          loadActiveRelationships();
          loadConcludedRelationships();
          var toast = document.createElement('div');
          toast.style.cssText = 'position:fixed;top:24px;right:24px;z-index:999;background:#22c55e;color:#fff;padding:14px 24px;border-radius:8px;font-size:.875rem;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.3)';
          toast.textContent = '✓ Approved: ' + name + ' ↔ ' + startup;
          document.body.appendChild(toast);
          setTimeout(function(){toast.remove()},3000);
        } else {
          alert('Error approving match: ' + data.message);
        }
      });
    });
  });

  // Reject — updates stats and DB
  document.querySelectorAll('.btn-reject').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var name = this.closest('.match-card').querySelector('.mc-name').textContent;
      var startup = document.getElementById('startupSelect').value;
      var self = this;
      
      fetch('api_relationships.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'reject', programme_id: progId, mentor_name: name, startup_name: startup })
      }).then(res => res.json()).then(data => {
        if (data.status === 'success') {
          self.closest('.match-card').style.opacity = '.4';
          self.closest('.match-card').style.pointerEvents = 'none';
          stats.rejected++; stats.awaiting = Math.max(0, stats.awaiting - 1);
          updateStats();
          loadActiveRelationships();
          loadConcludedRelationships();
          var toast = document.createElement('div');
          toast.style.cssText = 'position:fixed;top:24px;right:24px;z-index:999;background:var(--score-low);color:#fff;padding:14px 24px;border-radius:8px;font-size:.875rem;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.3)';
          toast.textContent = '✗ Rejected: ' + name;
          document.body.appendChild(toast);
          setTimeout(function(){toast.remove()},3000);
        } else {
          alert('Error rejecting match: ' + data.message);
        }
      });
    });
  });

  // Send for Review
  document.querySelectorAll('.btn-review').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var name = this.closest('.match-card').querySelector('.mc-name').textContent;
      this.textContent = '✓ Sent for Review'; this.disabled = true; this.style.opacity = '.6';
      alert(name + ' sent for human review.\nA notification has been sent to the Programme Lead.');
    });
  });

  // Override
  document.querySelectorAll('.btn-override').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var name = this.closest('.match-card').querySelector('.mc-name').textContent;
      if (confirm('Override eligibility rule for ' + name + '?\nThis will be logged for audit.')) {
        alert('Override applied. ' + name + ' moved to review queue.');
      }
    });
  });
}

// Old Dropdown switching removed

// ═══ Edit / End Programme buttons ═══
document.getElementById('editProgBtn').addEventListener('click', function() {
  // Pre-fill the modal with current DOM data before opening
  let currentName = document.querySelector('.page-title-row h1').childNodes[0].textContent.trim();
  document.getElementById('editName').value = currentName;
  
  let infoRows = document.querySelectorAll('.info-row');
  infoRows.forEach(row => {
    let lbl = row.querySelector('.info-label').textContent;
    if (lbl === 'Location') document.getElementById('editLoc').value = row.querySelector('.info-value').textContent;
  });
  
  document.getElementById('editModal').style.display = 'flex';
});
document.getElementById('endProgBtn').addEventListener('click', function() {
  document.getElementById('endModal').style.display = 'flex';
});

// ═══ API Integration for Edit Programme ═══
const urlParams = new URLSearchParams(window.location.search);
const progId = urlParams.get('id'); // Do not default to 1

// Route views based on URL parameter
let allProgrammes = [];
let currentFilter = 'all';

if (progId) {
  document.getElementById('detailView').classList.remove('hidden');
  document.getElementById('listView').classList.add('hidden');
  fetchDetailView();
} else {
  document.getElementById('listView').classList.remove('hidden');
  document.getElementById('detailView').classList.add('hidden');
  initListView();
}

function fetchDetailView() {
  fetch(`api_programme.php?id=${progId}`)
  .then(res => res.json())
  .then(data => {
     if (data.status === 'success') {
         const p = data.data;
         window.currentProgrammeStatus = p.status;
         let statusColor = p.status === 'active' ? 'active' : (p.status === 'pending' ? 'pending' : 'completed');
         document.querySelector('.page-title-row h1').innerHTML = p.programme_name + ' <span class="pill pill-' + statusColor + '" style="text-transform:capitalize">' + p.status + '</span>';
         document.querySelector('.breadcrumb').innerHTML = '<a href="dashboard.html">Dashboard</a> › <a href="programme.php">Programmes</a> › ' + p.programme_name;
         
         if (p.status === 'completed') {
             document.querySelector('.btn-generate').style.display = 'none';
             document.getElementById('editProgBtn').style.display = 'none';
             document.getElementById('endProgBtn').style.display = 'none';
         } else {
             document.querySelector('.btn-generate').style.display = 'inline-flex';
             document.getElementById('editProgBtn').style.display = 'inline-block';
             document.getElementById('endProgBtn').style.display = 'inline-block';
         }
         
         const infoRows = document.querySelectorAll('.info-row');
         infoRows.forEach(row => {
            const lbl = row.querySelector('.info-label').textContent;
            if (lbl === 'Location' && p.location) row.querySelector('.info-value').textContent = p.location;
            if (lbl === 'Started' && p.start_date) {
                let d = new Date(p.start_date);
                row.querySelector('.info-value').textContent = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
            }
            if (lbl === 'Ends' && p.end_date) {
                let d = new Date(p.end_date);
                row.querySelector('.info-value').textContent = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
            }
         });
         
         // Update inputs in the edit modal so dates are accurate
         document.getElementById('editName').value = p.programme_name;
         document.getElementById('editLoc').value = p.location;
         if (p.start_date) document.querySelectorAll('#editModal input[type="date"]')[0].value = p.start_date;
         if (p.end_date) document.querySelectorAll('#editModal input[type="date"]')[1].value = p.end_date;
          // Load Matches
          loadMatches();
     }
  });
}

function initListView() {
  loadProgrammesList();
  loadTemplatesDropdown();
  
  // Set up Launch modal trigger
  document.getElementById('btnLaunchProgList').addEventListener('click', function() {
    document.getElementById('launchModal').style.display = 'flex';
  });

  // Filter Chip clicks
  document.querySelectorAll('.filter-chip').forEach(chip => {
    chip.addEventListener('click', function() {
      document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      currentFilter = this.getAttribute('data-status');
      renderProgrammesGrid();
    });
  });

  // Search input typing
  document.getElementById('searchProgrammes').addEventListener('input', renderProgrammesGrid);
}

function loadProgrammesList() {
  const grid = document.getElementById('programmesListGrid');
  grid.innerHTML = '<div style="grid-column: span 4; text-align: center; padding: 40px; color: var(--text-muted);">Loading programmes...</div>';
  
  fetch('api_programme.php')
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        allProgrammes = data.data;
        renderProgrammesGrid();
      } else {
        grid.innerHTML = `<div style="grid-column: span 4; text-align: center; padding: 40px; color: var(--score-low);">Failed to load: ${data.message}</div>`;
      }
    })
    .catch(err => {
      grid.innerHTML = '<div style="grid-column: span 4; text-align: center; padding: 40px; color: var(--score-low);">Error communicating with server.</div>';
    });
}

function renderProgrammesGrid() {
  const grid = document.getElementById('programmesListGrid');
  const query = document.getElementById('searchProgrammes').value.toLowerCase().trim();
  grid.innerHTML = '';

  let filtered = allProgrammes;

  // Filter by status
  if (currentFilter !== 'all') {
    filtered = filtered.filter(p => p.status === currentFilter);
  }

  // Filter by search query
  if (query) {
    filtered = filtered.filter(p => p.programme_name.toLowerCase().includes(query) || (p.location && p.location.toLowerCase().includes(query)));
  }

  if (filtered.length === 0) {
    grid.innerHTML = '<div style="grid-column: span 4; text-align: center; padding: 40px; color: var(--text-muted);">No programmes found matching search criteria.</div>';
    return;
  }

  filtered.forEach(p => {
    const statusColor = p.status === 'active' ? 'active' : (p.status === 'pending' ? 'pending' : 'completed');
    const sd = p.start_date ? new Date(p.start_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : 'No start date';
    const ed = p.end_date ? new Date(p.end_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '';
    const dateStr = ed ? `${sd} - ${ed}` : sd;
    
    let card = document.createElement('div');
    card.className = 'card card-hover';
    card.style.cssText = 'display: flex; flex-direction: column; justify-content: space-between; min-height: 200px;';
    card.innerHTML = `
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 8px;">
          <span class="pill pill-${statusColor}" style="text-transform: capitalize;">${p.status}</span>
          <span style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            ${p.location || '-'}
          </span>
        </div>
        <h3 style="margin-bottom: 8px; font-size: 0.95rem; line-height: 1.4; font-weight:600;">${p.programme_name}</h3>
        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 16px;">Template: ${p.template_name || 'Custom'}</p>
      </div>
      <div style="border-top: 1px solid var(--border); padding-top: 12px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.73rem; color: var(--text-muted);">${dateStr}</span>
        <a href="programme.php?id=${p.id}" class="btn btn-secondary" style="font-size: 0.75rem; padding: 4px 12px; text-decoration:none;">Manage →</a>
      </div>
    `;
    grid.appendChild(card);
  });
}

function loadTemplatesDropdown() {
  fetch('api_templates.php')
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        const sel = document.getElementById('launchTemplate');
        sel.innerHTML = '<option value="">Select a template...</option>';
        data.data.forEach(t => {
          sel.innerHTML += `<option value="${t.id}">${t.template_name}</option>`;
        });
      }
    });
}

// Handle Launch Programme from Programmes page list
document.getElementById('saveLaunchBtn').addEventListener('click', function() {
  const btn = this;
  const payload = {
    programme_name: document.getElementById('launchName').value.trim(),
    template_id: document.getElementById('launchTemplate').value,
    location: document.getElementById('launchLoc').value.trim(),
    start_date: document.getElementById('launchStart').value,
    end_date: document.getElementById('launchEnd').value,
    status: 'pending'
  };
  
  if (!payload.programme_name) {
    alert("Please enter a programme name.");
    return;
  }
  
  btn.textContent = 'Launching...';
  btn.disabled = true;
  
  fetch('api_programme.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  .then(res => res.json())
  .then(data => {
    btn.textContent = 'Launch Programme';
    btn.disabled = false;
    if (data.status === 'success') {
      document.getElementById('launchModal').style.display = 'none';
      // Reset form
      document.getElementById('launchName').value = '';
      document.getElementById('launchLoc').value = '';
      document.getElementById('launchStart').value = '';
      document.getElementById('launchEnd').value = '';
      
      // Reload list
      loadProgrammesList();
      
      // Show toast
      var toast = document.createElement('div');
      toast.style.cssText = 'position:fixed;top:24px;right:24px;z-index:999;background:#22c55e;color:#fff;padding:14px 24px;border-radius:8px;font-size:.875rem;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.3)';
      toast.textContent = '✓ Programme launched successfully!';
      document.body.appendChild(toast);
      setTimeout(function(){toast.remove()},3000);
    } else {
      alert('Error launching programme: ' + data.message);
    }
  }).catch(err => {
    btn.textContent = 'Launch Programme';
    btn.disabled = false;
    alert('Network error');
  });
});


document.getElementById('saveProgrammeBtn').addEventListener('click', function() {
  const btn = this;
  btn.textContent = 'Saving...';
  btn.disabled = true;

  const payload = {
    id: progId,
    programme_name: document.getElementById('editName').value.trim(),
    location: document.getElementById('editLoc').value.trim(),
    start_date: document.querySelectorAll('#editModal input[type="date"]')[0].value,
    end_date: document.querySelectorAll('#editModal input[type="date"]')[1].value
  };

  fetch('api_programme.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  .then(res => res.json())
  .then(data => {
    btn.textContent = 'Save Changes';
    btn.disabled = false;
    if (data.status === 'success') {
      // Update DOM
      let statusColor = window.currentProgrammeStatus === 'active' ? 'active' : (window.currentProgrammeStatus === 'pending' ? 'pending' : 'completed');
      document.querySelector('.page-title-row h1').innerHTML = payload.programme_name + ' <span class="pill pill-' + statusColor + '" style="text-transform:capitalize">' + window.currentProgrammeStatus + '</span>';
      
      const infoRows = document.querySelectorAll('.info-row');
      infoRows.forEach(row => {
         const lbl = row.querySelector('.info-label').textContent;
         if (lbl === 'Location') row.querySelector('.info-value').textContent = payload.location;
         if (lbl === 'Started' && payload.start_date) {
             let d = new Date(payload.start_date);
             row.querySelector('.info-value').textContent = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
         }
         if (lbl === 'Ends' && payload.end_date) {
             let d = new Date(payload.end_date);
             row.querySelector('.info-value').textContent = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
         }
      });
      
      // Update Breadcrumb
      document.querySelector('.breadcrumb').innerHTML = '<a href="dashboard.html">Dashboard</a> › <a href="programme.php">Programmes</a> › ' + payload.programme_name;
      
      // Success toast
      var t=document.createElement('div');
      t.style.cssText='position:fixed;top:24px;right:24px;z-index:999;background:#22c55e;color:#fff;padding:14px 24px;border-radius:8px;font-size:.875rem;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.3)';
      t.textContent='✓ Programme updated successfully!';
      document.body.appendChild(t);
      setTimeout(function(){t.remove()},3000);
      document.getElementById('editModal').style.display='none';
    } else {
      alert('Error updating programme: ' + data.message);
    }
  })
  .catch(err => {
    btn.textContent = 'Save Changes';
    btn.disabled = false;
    alert('Failed to connect to API');
  });
});

document.getElementById('confirmEndProgBtn').addEventListener('click', function() {
  const btn = this;
  btn.textContent = 'Ending...';
  btn.disabled = true;
  
  fetch('api_programme.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: progId, status: 'completed' })
  })
  .then(res => res.json())
  .then(data => {
    btn.textContent = 'Confirm End Programme';
    btn.disabled = false;
    if (data.status === 'success') {
      window.currentProgrammeStatus = 'completed';
      document.querySelector('.page-title-row h1 .pill').textContent = 'Completed';
      document.querySelector('.page-title-row h1 .pill').className = 'pill pill-completed';
      document.getElementById('endModal').style.display = 'none';
      
      // Hide controls
      document.querySelector('.btn-generate').style.display = 'none';
      document.getElementById('editProgBtn').style.display = 'none';
      document.getElementById('endProgBtn').style.display = 'none';
      
      alert('Programme ended and marked as Completed.');
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(err => {
    btn.textContent = 'Confirm End Programme';
    btn.disabled = false;
    alert('Failed to connect to API');
  });
});

// ═══ Load Matches Dynamically ═══
function loadMatches() {
  const container = document.getElementById('matchesContainer');
  container.innerHTML = '<div style="padding:40px;text-align:center;color:var(--text-muted)">Loading matches...</div>';
  fetch('api_relationships.php?programme_id=' + progId)
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        if (data.data.length === 0) {
           container.innerHTML = '<div style="padding:40px;text-align:center;color:var(--text-muted);background:var(--card-bg);border-radius:12px;border:1px dashed var(--border)">No matches found. Click Generate Matches to run the AI engine.</div>';
           return;
        }
        
        // Setup dropdown
        const sel = document.getElementById('startupSelect');
        const currentStartup = sel.value;
        const startups = [];
        for (let i = 0; i < data.data.length; i++) {
            let name = data.data[i].startup_name;
            if (startups.indexOf(name) === -1) {
                startups.push(name);
            }
        }
        sel.innerHTML = '<option value="">All Startups</option>';
        for (let i = 0; i < startups.length; i++) {
            let s = startups[i];
            sel.innerHTML += `<option value="${s}">${s}</option>`;
        }
        if (currentStartup && startups.indexOf(currentStartup) !== -1) {
            sel.value = currentStartup;
        }
        
        stats.proposed = data.data.length;
        stats.approved = 0;
        stats.rejected = 0;
        stats.awaiting = 0;
        stats.scores = [];
        for (let i = 0; i < data.data.length; i++) {
            let r = data.data[i];
            if (r.status === 'active') stats.approved++;
            else if (r.status === 'rejected') stats.rejected++;
            else if (r.status === 'proposed') stats.awaiting++;
            
            let score = parseFloat(r.match_score);
            if (score > 0) stats.scores.push(score);
        }
        updateStats();
        
        renderMatches(data.data);
      } else {
        container.innerHTML = '<div style="padding:40px;text-align:center;color:var(--score-low)">Failed to load matches: ' + data.message + '</div>';
      }
    })
    .catch(err => {
      container.innerHTML = '<div style="padding:40px;text-align:center;color:var(--score-low)">Network or server error while loading matches.</div>';
      console.error(err);
    });
}

function renderMatches(matches) {
  const container = document.getElementById('matchesContainer');
  const sel = document.getElementById('startupSelect').value;
  container.innerHTML = '';
  
  let filtered = [];
  if (sel) {
      for (let i = 0; i < matches.length; i++) {
          if (matches[i].startup_name === sel) {
              filtered.push(matches[i]);
          }
      }
  } else {
      filtered = matches;
  }
  
  if (filtered.length === 0) {
      container.innerHTML = '<div style="padding:40px;text-align:center;color:var(--text-muted)">No matches for this startup.</div>';
      return;
  }
  
  for (let i = 0; i < filtered.length; i++) {
      let m = filtered[i];
      let scoreClass = 'score-high';
      if (m.match_score < 70) scoreClass = 'score-low';
      else if (m.match_score < 85) scoreClass = 'score-mid';
      
      let nameParts = m.mentor_name.split(' ');
      let initials = '';
      for (let j = 0; j < nameParts.length; j++) {
          if (nameParts[j]) initials += nameParts[j][0];
      }
      initials = initials.substring(0, 2);

      let riskHtml = '';
      if (m.risk_flags && m.risk_flags.length > 0) {
          for (let j = 0; j < m.risk_flags.length; j++) {
             riskHtml += `<span class="mc-risk mc-risk-amber">${m.risk_flags[j]}</span>`;
          }
      } else {
          riskHtml = `<span class="mc-risk mc-risk-green">✓ No risks flagged</span>`;
      }
      
      let tagsHtml = '';
      if (m.mentor_expertise) {
          for (let j = 0; j < m.mentor_expertise.length; j++) {
              tagsHtml += `<span class="mc-tag">${m.mentor_expertise[j]}</span>`;
          }
      }
      
      let bannerHtml = '';
      if (m.needs_human_review == 1) {
          bannerHtml = `<div class="mc-banner mc-banner-amber">⚠️ Needs Human Review — low confidence or risks detected</div>`;
      }
      
      let card = document.createElement('div');
      card.className = 'match-card ' + (m.needs_human_review == 1 ? 'needs-review' : '');
      card.innerHTML = `
        ${bannerHtml}
        <div class="mc-top">
          <div class="mc-identity"><div class="mc-avatar" style="background:#6366f1">${initials}</div><div><div class="mc-name">${m.mentor_name} <span style="font-size:0.75rem;color:var(--text-muted)">↔ ${m.startup_name}</span></div></div></div>
          <div class="mc-score ${scoreClass}">${Math.round(m.match_score)}</div>
        </div>
        <div class="mc-tags">${tagsHtml}</div>
        <div class="mc-reasoning">${m.match_reasoning || '<em style="color:var(--text-muted)">No AI reasoning available for this match.</em>'}</div>
        <div class="mc-risks">${riskHtml}</div>
        <div class="mc-actions">
           ${m.status === 'proposed' ? `
           <button class="btn-approve" onclick="updateRel('${m.mentor_name}', '${m.startup_name}', 'approve', ${m.match_score})">✓ Approve</button>
           <button class="btn-reject" onclick="updateRel('${m.mentor_name}', '${m.startup_name}', 'reject', ${m.match_score})">Reject</button>
           ` : `<span class="pill pill-${m.status === 'active' ? 'active' : 'completed'}" style="text-transform:capitalize">${m.status}</span>`}
        </div>
      `;
      container.appendChild(card);
  }
}

document.getElementById('startupSelect').addEventListener('change', loadMatches);

// ═══ Generate Matches ═══
document.querySelector('.btn-generate').addEventListener('click', function() {
  var self = this;
  self.textContent = '⏳ Generating AI Matches...'; self.disabled = true;

  // Abort if the request takes longer than 5 minutes (Gemini + retries + rate-limit delays)
  var controller = new AbortController();
  var timeout = setTimeout(function() { controller.abort(); }, 300000);

  fetch('api_generate_matches.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ programme_id: progId }),
      signal: controller.signal
  })
  .then(function(res) {
      // Read as text first — PHP fatal errors produce non-JSON output
      return res.text().then(function(text) {
          return { ok: res.ok, status: res.status, body: text };
      });
  })
  .then(function(result) {
      if (!result.ok) {
          throw new Error('Server returned HTTP ' + result.status);
      }
      var data;
      try {
          data = JSON.parse(result.body);
      } catch (e) {
          console.error('Non-JSON API response:', result.body.substring(0, 500));
          throw new Error('The server returned an invalid response. Check the PHP error log.');
      }
      if (data.status === 'success') {
          var toast = document.createElement('div');
          toast.style.cssText = 'position:fixed;top:24px;right:24px;z-index:999;background:#22c55e;color:#fff;padding:14px 24px;border-radius:8px;font-size:.875rem;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.3)';
          toast.textContent = '✓ AI generated ' + (data.matches_processed || 0) + ' match proposals.';
          document.body.appendChild(toast);
          setTimeout(function(){ toast.remove(); }, 4000);
          loadMatches();
      } else {
          alert('Error: ' + (data.message || 'Unknown error from AI engine.'));
      }
  })
  .catch(function(err) {
      if (err.name === 'AbortError') {
          alert('The AI matching request timed out after 5 minutes. Try reducing the number of candidates or try again.');
      } else {
          alert('Error communicating with AI engine: ' + err.message);
      }
      console.error('Generate Matches error:', err);
  })
  .finally(function() {
      clearTimeout(timeout);
      self.textContent = '✨ Generate Matches';
      self.disabled = false;
  });
});

function updateRel(mentorName, startupName, action, score) {
    fetch('api_relationships.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: action, programme_id: progId, mentor_name: mentorName, startup_name: startupName, match_score: score })
    }).then(res => res.json()).then(data => {
        if (data.status === 'success') {
            loadMatches();
            loadActiveRelationships();
            loadConcludedRelationships();
        }
    });
}

function loadActiveRelationships() {
  const container = document.getElementById('activeRelsContainer');
  if (!container) return;
  fetch('api_relationships.php?programme_id=' + progId + '&status=active')
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        const countBadge = document.getElementById('activeRelsCount');
        if (countBadge) countBadge.textContent = data.data.length;
        if (data.data.length === 0) {
          container.innerHTML = '<div class="card empty-state">No active relationships yet.</div>';
          return;
        }
        let html = '';
        for (let i = 0; i < data.data.length; i++) {
          let r = data.data[i];
          let daysActive = 0;
          let startedText = 'Not started';
          if (r.activated_at) {
            let activatedDate = new Date(r.activated_at.replace(/-/g, "/"));
            let diffTime = Math.abs(new Date() - activatedDate);
            daysActive = Math.max(1, Math.round(diffTime / (1000 * 60 * 60 * 24)));
            startedText = 'Started ' + activatedDate.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
          }
          let progressWidth = Math.min(100, Math.max(2, daysActive * 2));
          html += '<div class="rel-card">';
          html += '<div class="rel-parties">' + r.mentor_name + ' ↔ ' + r.startup_name + '</div>';
          html += '<div class="rel-meta">' + startedText + ' · ' + daysActive + ' days active</div>';
          html += '<div class="rel-progress"><div class="rel-progress-fill" style="width:' + progressWidth + '%"></div></div>';
          html += '<button class="btn btn-ghost" style="font-size:.6875rem;margin-top:6px;padding:2px 8px">Conclude</button>';
          html += '</div>';
        }
        container.innerHTML = html;
      }
    });
}

function loadConcludedRelationships() {
  const container = document.getElementById('concludedRelsContainer');
  if (!container) return;
  fetch('api_relationships.php?programme_id=' + progId + '&status=concluded')
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        const countBadge = document.getElementById('concludedRelsCount');
        if (countBadge) countBadge.textContent = data.data.length;
        if (data.data.length === 0) {
          container.innerHTML = '<div class="card empty-state"><div style="font-size:1.5rem;margin-bottom:8px;opacity:.3">📋</div>No concluded relationships yet.<br>When you wrap up a mentorship, capture the outcome here to feed the learning loop.</div>';
          return;
        }
        let html = '';
        for (let i = 0; i < data.data.length; i++) {
          let r = data.data[i];
          html += '<div class="rel-card" style="opacity:0.8">';
          html += '<div class="rel-parties">' + r.mentor_name + ' ↔ ' + r.startup_name + '</div>';
          html += '<div class="rel-meta">Concluded</div>';
          html += '</div>';
        }
        container.innerHTML = html;
      }
    });
}

// ═══ Conclude buttons Event Delegation ═══
if (document.getElementById('activeRelsContainer')) {
  document.getElementById('activeRelsContainer').addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-ghost') && e.target.textContent.trim() === 'Conclude') {
      var card = e.target.closest('.rel-card');
      var parties = card.querySelector('.rel-parties').textContent;
      var parts = parties.split('↔').map(p => p.trim());
      var mentorName = parts[0];
      var startupName = parts[1];
      
      if (confirm('Conclude relationship: ' + parties + '?\nYou will be prompted to capture the outcome.')) {
        fetch('api_relationships.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'conclude', programme_id: progId, mentor_name: mentorName, startup_name: startupName })
        }).then(res => res.json()).then(data => {
           if (data.status === 'success') {
             alert('Relationship concluded. Outcome recorded for the learning loop.');
             loadActiveRelationships();
             loadConcludedRelationships();
           } else {
             alert('Error concluding relationship: ' + data.message);
           }
        });
      }
    }
  });
}

// ═══ Init ═══
bindCardButtons();
loadMatches(); // Always load matches on page init
if (typeof progId !== 'undefined' && progId) {
  loadActiveRelationships();
  loadConcludedRelationships();
}

// Close modals & dropdowns
document.addEventListener('click', function(e) {
  document.querySelectorAll('.avatar-dropdown.open').forEach(function(d) { if (!d.parentElement.contains(e.target)) d.classList.remove('open'); });
  if (e.target.classList.contains('modal-overlay')) e.target.style.display = 'none';
});
</script>
</body>
</html>
