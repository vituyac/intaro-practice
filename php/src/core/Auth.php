<?php
    namespace App\core;

    class Auth {

        public function isLoggedIn(): bool {
            return isset($_SESSION['user_id']);
        }

        public function getUserId(): ?int {
            return $_SESSION['user_id'] ?? null;
        }
    }
?>