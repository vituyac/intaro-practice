<?php

    namespace App\utils;

    class PaternsFormOrder {
        public static function get(): array {
            return [
                'surname' => '/^[А-ЯЁ][а-яё-]+$/u',
                'firstname' => '/^[А-ЯЁ][а-яё-]+$/u',
                'lastname' => '/^[А-ЯЁ][а-яё-]+$/u',
                'phone' => '/^\+7\d{10}$/',
                'address' => '/^[А-ЯЁа-яё0-9\s,.-]+$/u',
            ];
        }
    }

?>