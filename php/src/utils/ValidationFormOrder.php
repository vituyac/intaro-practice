<?php

    namespace App\utils;

    class ValidationFormOrder {
        public static function validate(string $data, string $pattern): bool {
            return preg_match($pattern, $data);
        }
    }

?>