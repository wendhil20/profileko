<?php
// routes/web.php — listahan ng routes
// 'url' => ['page' => file sa pages/ (walang .php), 'title' => page title]
// 'fullscreen' => true  → walang footer (para sa game-style home)
// 'admin'      => true  → admin sidebar + kailangang naka-login
// 'bare'       => true  → walang sidebar at footer (hal. login page)
// 'controller' => 'admin/ProjectController' → GET = index(), POST = store()
//                 (file sa controllers/, walang .php)
// 'actions'    => ['GET' => 'edit', 'POST' => 'update'] → ibang method ang tatawagin

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

$routes = [
    // ===== USER (public) =====
    'home'     => ['controller' => 'user/HomeController', 'title' => 'Home', 'fullscreen' => true],
    'resume'          => ['controller' => 'user/ResumeController', 'title' => 'Resume'],
    'resume/download' => ['controller' => 'user/ResumeController', 'title' => 'Resume',
                          'actions' => ['GET' => 'download', 'POST' => 'download']],
    'projects' => ['controller' => 'user/PublicProjectController', 'title' => 'Projects'],
    'contact'  => ['page' => 'user/contact',  'title' => 'Contact'],

    // ===== ADMIN =====
    'loginadmin'     => ['page' => 'admin/auth/loginadmin', 'title' => 'Admin Login', 'bare' => true],
    'admindashboard' => ['page' => 'admin/dashboard',       'title' => 'Dashboard',       'admin' => true],
    'adminprojects'      => ['controller' => 'admin/ProjectController', 'title' => 'Manage Projects', 'admin' => true],
    'adminprojects/edit' => ['controller' => 'admin/ProjectController', 'title' => 'Edit Project',    'admin' => true,
                             'actions' => ['GET' => 'edit', 'POST' => 'update']],
    'adminresume'        => ['controller' => 'admin/ResumeController',  'title' => 'Resume',          'admin' => true],
    // 'admin/logout' — hawak na ng portfolio.php, hindi na kailangan ng page
];