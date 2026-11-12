<?php
// controllers/Controller.php — base ng lahat ng controller
//
// Bawat method ng controller ay:
//   • nagbabalik ng $this->view('folder/page', [...data]) → ipapakita ang page
//   • o tumatawag ng redirect('route') → lilipat ng page (hal. pagkatapos mag-save)

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

abstract class Controller
{
    /** Aling page ang ipapakita, at anong variables ang gagamitin nito */
    protected function view(string $page, array $data = []): array
    {
        return [$page, $data];
    }
}