<?php
namespace App\controllers;

use App\models\User;
use App\services\RetailCrmService;

class UserController {
    private User $userModel;
    private RetailCrmService $srm;

    public function __construct() {
        $this->userModel = new User();
        $this->srm = new RetailCrmService();
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            $email = isset($input['email']) ? trim($input['email']) : null;
            $password = $input['password'] ?? null;
            $confirm = $input['confirm'] ?? null;

            if ($password !== $confirm) {
                http_response_code(400);
                echo json_encode(['error' => 'Пароли не совпадают']);
                exit();
            }

            if ($this->userModel->getUserByEmail($email)) {
                http_response_code(409);
                echo json_encode(['error' => 'Пользователь с таким email уже существует']);
                exit();
            }

            $userId = $this->userModel->createUser($email, $password);

            if ($userId) {
                $srm_id = $this->srm->registerUser($userId, $email);
                if ($srm_id) {
                    $this->userModel->setExternalID($userId, $srm_id);
                    http_response_code(200);
                    exit();
                }
            }

            http_response_code(500);
            exit();
        }
    }
}