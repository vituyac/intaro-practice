<?php
namespace App\core;

class Auth {
    public static function isAuthenticated() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            exit(json_encode(['error' => 'Недостаточно прав']));
        }
        return true;
    }
}
?>