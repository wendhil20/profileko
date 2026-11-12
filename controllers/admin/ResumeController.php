<?php
// controllers/admin/ResumeController.php — edit the resume (table: portresume, one row id = 1)
//
// Route:
//   'adminresume' → GET  = index()  (form with the current resume)
//                   POST = store()  (save the whole resume)

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

class ResumeController extends Controller
{
    private const MAX_EXPERIENCE = 15;
    private const MAX_EDUCATION  = 10;
    private const MAX_SKILLS     = 15;
    private const MAX_CERTS      = 30;
    private const MAX_BULLETS    = 40;   // lines per experience (headings + bullets)

    /** GET — the form, filled with the saved resume (or what was typed, when there are errors) */
    public function index(array $errors = [], ?array $resume = null): array
    {
        return $this->view('admin/page-3/resume', [
            'resume' => $resume ?? resume_load(),
            'errors' => $errors,
            'saved'  => isset($_GET['saved']),
        ]);
    }

    /** POST — validate and save everything */
    public function store(): array
    {
        if (!csrf_check()) {
            return $this->index(['form' => 'This form has expired. Please refresh the page and try again.']);
        }

        $resume = $this->collect();
        $errors = $this->validate($resume);
        if ($errors) {
            return $this->index($errors, $resume);   // keep what was typed
        }

        $json = fn(array $list) => json_encode(array_values($list), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        try {
            db_query(
                'INSERT INTO portresume
                    (id, full_name, title, email, phone, address, linkedin, github, website, summary,
                     experience_json, education_json, skills_json, certifications_json)
                 VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    full_name = VALUES(full_name), title = VALUES(title), email = VALUES(email),
                    phone = VALUES(phone), address = VALUES(address), linkedin = VALUES(linkedin),
                    github = VALUES(github), website = VALUES(website), summary = VALUES(summary),
                    experience_json = VALUES(experience_json), education_json = VALUES(education_json),
                    skills_json = VALUES(skills_json), certifications_json = VALUES(certifications_json)',
                [
                    $resume['full_name'], $resume['title'], $resume['email'], $resume['phone'],
                    $resume['address'], $resume['linkedin'], $resume['github'], $resume['website'],
                    $resume['summary'],
                    $json($resume['experience']), $json($resume['education']),
                    $json($resume['skills']), $json($resume['certifications']),
                ]
            );
        } catch (Throwable $ex) {
            error_log('Save resume failed: ' . $ex->getMessage());
            return $this->index(['form' => 'Could not save the resume. Please try again.'], $resume);
        }

        if (function_exists('flash')) {
            flash('success', 'Resume saved.');
        }
        redirect('adminresume?saved=1');   // so a refresh doesn't submit again
    }

    /* =========================================================
     |  PRIVATE
     * ========================================================= */

    /** Read the form into the same shape resume_load() returns (empty rows skipped) */
    private function collect(): array
    {
        $str = fn($v, int $max = 255) => mb_substr(trim((string) $v), 0, $max);

        $resume = resume_defaults();
        foreach (['full_name', 'title', 'email', 'phone', 'address', 'linkedin', 'github', 'website'] as $key) {
            $resume[$key] = $str($_POST[$key] ?? '');
        }
        $resume['summary'] = $str($_POST['summary'] ?? '', 2000);

        // Experience
        foreach (array_slice((array) ($_POST['exp'] ?? []), 0, self::MAX_EXPERIENCE) as $row) {
            if (!is_array($row)) continue;
            $bullets = preg_split('/\R/', (string) ($row['bullets'] ?? ''));
            $bullets = array_slice(array_values(array_filter(array_map(
                fn($b) => $str(ltrim($b, "-•* \t"), 500),
                $bullets
            ), fn($b) => $b !== '')), 0, self::MAX_BULLETS);

            $item = [
                'title'    => $str($row['title'] ?? '', 150),
                'company'  => $str($row['company'] ?? '', 150),
                'location' => $str($row['location'] ?? '', 150),
                'start'    => $str($row['start'] ?? '', 50),
                'end'      => $str($row['end'] ?? '', 50),
                'current'  => !empty($row['current']),
                'tools'    => $str($row['tools'] ?? '', 500),
                'bullets'  => $bullets,
            ];
            if ($item['title'] !== '' || $item['company'] !== '') {
                $resume['experience'][] = $item;
            }
        }

        // Education
        foreach (array_slice((array) ($_POST['edu'] ?? []), 0, self::MAX_EDUCATION) as $row) {
            if (!is_array($row)) continue;
            $item = [
                'degree'   => $str($row['degree'] ?? '', 200),
                'school'   => $str($row['school'] ?? '', 200),
                'location' => $str($row['location'] ?? '', 255),
                'years'    => $str($row['years'] ?? '', 50),
                'note'     => $str($row['note'] ?? '', 150),
            ];
            if ($item['degree'] !== '' || $item['school'] !== '') {
                $resume['education'][] = $item;
            }
        }

        // Skills (a label + comma-separated items)
        foreach (array_slice((array) ($_POST['skill'] ?? []), 0, self::MAX_SKILLS) as $row) {
            if (!is_array($row)) continue;
            $item = [
                'label' => $str($row['label'] ?? '', 100),
                'items' => $str($row['items'] ?? '', 500),
            ];
            if ($item['items'] !== '') {
                $resume['skills'][] = $item;
            }
        }

        // Certifications
        foreach (array_slice((array) ($_POST['cert'] ?? []), 0, self::MAX_CERTS) as $row) {
            if (!is_array($row)) continue;
            $item = [
                'name'   => $str($row['name'] ?? '', 200),
                'issuer' => $str($row['issuer'] ?? '', 150),
                'year'   => $str($row['year'] ?? '', 20),
            ];
            if ($item['name'] !== '') {
                $resume['certifications'][] = $item;
            }
        }

        return $resume;
    }

    private function validate(array $r): array
    {
        $errors = [];

        if ($r['full_name'] === '') {
            $errors['full_name'] = 'Please enter your full name.';
        }
        if ($r['email'] !== '' && !filter_var($r['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        foreach (['linkedin' => 'LinkedIn', 'github' => 'GitHub', 'website' => 'Website'] as $key => $label) {
            if ($r[$key] !== '' && !preg_match('#^https?://#i', $r[$key])) {
                $errors[$key] = "$label link must start with https://";
            }
        }
        foreach ($r['experience'] as $i => $exp) {
            if ($exp['title'] === '' || $exp['company'] === '') {
                $errors['exp'] = 'Each experience needs a job title and a company (row ' . ($i + 1) . ').';
                break;
            }
        }

        return $errors;
    }
}