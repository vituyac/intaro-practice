<?php
namespace App\core;

class Auth {
    public static function isAuthenticated() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit(json_encode(['error' => 'Требуется аутентификация']));
        }
        return true;
    }
}
?>