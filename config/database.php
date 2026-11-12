<?php
// config/database.php — koneksyon sa MySQL (mysqli)
// Naka-.env ang settings:
//   DB_HOST=localhost
//   DB_PORT=3306
//   DB_NAME=portfolio
//   DB_USER=root
//   DB_PASS=
//
// Gamit:  $conn = db();
// Kumokonekta lang kapag tinawag ang db(), kaya ang mga page na walang database
// (hal. home) ay gagana pa rin kahit naka-off ang MySQL.

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

if (!function_exists('db')) {
    function db(): mysqli
    {
        static $conn = null;
        if ($conn instanceof mysqli) {
            return $conn;
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);   // error → exception

        try {
            $conn = new mysqli(
                (string) env('DB_HOST', 'localhost'),
                (string) env('DB_USER', 'root'),
                (string) env('DB_PASS', ''),
                (string) env('DB_NAME', 'portfolio'),
                (int) env('DB_PORT', 3306)
            );
            $conn->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            error_log('[db] ' . $e->getMessage());   // detalye sa log lang, hindi sa screen
            http_response_code(500);
            exit('Hindi makakonekta sa database. Pakisubukan ulit mamaya.');
        }

        return $conn;
    }
}