<?php
namespace App\utils;

class FormValidator
{
    public static function validateLogin(array $data): ?array
    {
        $errors = [];

        $required = ['email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "Поле {$field} обязательно";
            }
        }

        return $errors ?: null;
    }

    public static function validateRegister(array $data): ?array
    {
        $errors = [];

        $required = ['email', 'password', 'confirm', 'firstName', 'lastName'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "Поле {$field} обязательно";
            }
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный формат email';
        }

        if (!empty($data['password'])) {
            $password = $data['password'];
            if (strlen($password) < 8) {
                $errors[] = 'Пароль должен содержать минимум 8 символов';
            }
            if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
                $errors[] = 'Пароль должен содержать хотя бы одну букву и одну цифру';
            }
        }

        if (!empty($data['password']) && !empty($data['confirm']) && $data['password'] !== $data['confirm']) {
            $errors[] = 'Пароли не совпадают';
        }

        return $errors ?: null;
    }
}