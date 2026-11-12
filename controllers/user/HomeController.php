<?php
// controllers/user/HomeController.php — Home (profile) page
//
// Route: 'home' → GET = index()
// Kinukuha ang mga pinakabagong project para sa "Featured Projects" sa kanan ng home.

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

class HomeController extends Controller
{
    /** Ilang project ang iikot sa home */
    private const FEATURED = 5;

    /** GET — home page kasama ang mga featured project (pangalan + cover) */
    public function index(): array
    {
        $featured = db_all(
            'SELECT id, name, image
               FROM portproject
              ORDER BY id DESC
              LIMIT ' . self::FEATURED
        );

        return $this->view('user/home', ['featured' => $featured]);
    }
}