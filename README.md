# EcosystemOS Matching Engine

EcosystemOS is an autonomous mentor-startup matching platform powered by Gemini AI. It calculates match scores, generates reasoning, and assigns risk flags based on programme rules, entity profiles, and historical outcomes.

## Repository Structure

- `api_*.php`: Core backend handlers serving JSON endpoints.
- `api_generate_matches.php`: The primary engine integrating with the Gemini API to evaluate startup-mentor pairs using an exponential backoff retry mechanism.
- `*.html`: Frontend UI templates (Dashboard, Programmes, Templates, Entities, Insights).
- `css/`: Styling assets.
- `secure_keys/gemini_key.json`: The secure local storage file for your Gemini API key.

## Setup Instructions

### 1. Database Configuration
1. Import the consolidated database schema and seed data directly from `cradle_hackathon.sql`:
   - Import `cradle_hackathon.sql` using phpMyAdmin or command line:
     ```bash
     mysql -u root < cradle_hackathon.sql
     ```
   - This single file automatically creates the `cradle_hackathon` database, creates all necessary tables, and populates the seed data.
2. Configure your database credentials in `db.php` if they differ from the default (`root` with no password).

### 2. API Key & Credentials Setup
1. Duplicate the template files:
   - Copy `secure_keys/gemini_key.json.example` to `secure_keys/gemini_key.json`
   - Copy `secure_keys/service-account-credentials.json.example` to `secure_keys/service-account-credentials.json`
2. Obtain a Gemini API key from Google AI Studio and place it in `secure_keys/gemini_key.json`.
3. Provide your Google Cloud Service Account credentials in `secure_keys/service-account-credentials.json` for Vertex AI features.
4. **Security Notice:**
   - The `.gitignore` file is pre-configured to ignore `secure_keys/gemini_key.json` and `secure_keys/service-account-credentials.json`. **Never commit these active credential files to GitHub.**
   - In a production environment, ensure the `secure_keys` directory has restricted permissions (e.g., `chmod 600` or restricted via Windows `icacls`) so that only the web server process can read it.

### 3. Running the Project
1. Host the project directory on a local web server (e.g., Apache via XAMPP or Nginx) running PHP 8+.
2. Access `dashboard.html` through your web browser (e.g., `http://localhost/cradle_hackathon_26/dashboard.html`).
3. Navigate to a Programme (e.g., CIP SPARK Cohort 12) and click **Generate Matches** to test the Gemini AI integration.

## SSL Note
The current implementation uses `CURLOPT_SSL_VERIFYPEER => false` in `api_generate_matches.php` to facilitate local testing without a configured SSL certificate. For production deployment, ensure a valid SSL certificate is installed on your server and remove this bypass.
