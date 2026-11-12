<?php
// controllers/user/ResumeController.php — public resume page + PDF download (dompdf)
//
// Routes:
//   'resume'          → GET = index()     (resume page)
//   'resume/download' → GET = download()  (PDF file)

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

use Dompdf\Dompdf;
use Dompdf\Options;

class ResumeController extends Controller
{
    /** GET /resume */
    public function index(): array
    {
        return $this->view('user/resume', [
            'resume' => resume_load(),
        ]);
    }

    /** GET /resume/download — builds the PDF from pages/user/resume-pdf.php */
    public function download(): array
    {
        $resume = resume_load();

        if (!class_exists(Dompdf::class)) {
            http_response_code(500);
            exit('PDF library not installed. Run: composer require dompdf/dompdf');
        }

        // Render the PDF template into a string
        ob_start();
        include ROOT_PATH . 'pages/user/resume-pdf.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');   // has ₱, – and • characters
        $options->set('isRemoteEnabled', false);       // no outside files needed
        $options->set('chroot', ROOT_PATH);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // portfolio.php started ob_start() — clear everything so only the PDF is sent
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $name = trim(preg_replace('/[^A-Za-z0-9]+/', '-', $resume['full_name'] ?: 'Resume'), '-');
        $dompdf->stream($name . '-Resume.pdf', ['Attachment' => true]);
        exit;
    }
}