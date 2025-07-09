<?php
namespace App\controllers;

use App\models\User;
use App\services\RetailCrmService;
use App\utils\FormValidator;
use App\core\Auth;

class UserController
{
    private User $userModel;
    private RetailCrmService $srm;

    public function __construct()
    {
        $this->userModel = new User();
        $this->srm = new RetailCrmService();
    }

    public function register(): void
    {
        // DEBUG LOGGING: mark controller entry and input
        file_put_contents('/tmp/php_register_debug.log', "\n==== REGISTER CONTROLLER CALLED ====\n", FILE_APPEND);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            file_put_contents('/tmp/php_register_debug.log', "INPUT:\n" . print_r($input, true), FILE_APPEND);

            $errors = FormValidator::validateRegister($input);
            if ($errors) {
                http_response_code(400);
                echo json_encode(['success' => false, 'details' => $errors]);
                exit();
            }

            $email = trim($input['email']);
            $password = $input['password'];
            $firstName = $input['firstName'];
            $lastName = $input['lastName'];
            $patronymic = $input['patronymic'] ?? null;
            $phone = $input['phone'] ?? '';
            $birthday = $input['birthday'] ?? '';
            $sex = $input['sex'] ?? '';

            if ($this->userModel->getUserByEmail($email)) {
                http_response_code(409);
                echo json_encode(['success' => false, 'error' => 'Пользователь с таким email уже существует']);
                exit();
            }

            $userId = $this->userModel->createUser($email, $password);

            if ($userId) {
                $srm_id = $this->srm->registerUser($userId, $email, $firstName, $lastName, $patronymic, $phone, $birthday, $sex);
                if ($srm_id) {
                    $this->userModel->setExternalID($userId, $userId);
                    http_response_code(200);
                    echo json_encode(['success' => true]);
                    exit();
                }
            }
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Ошибка регистрации']);
            exit();
        }
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            $errors = FormValidator::validateLogin($input);
            if ($errors) {
                http_response_code(400);
                echo json_encode(['details' => $errors]);
                exit();
            }

            $email = trim($input['email']);
            $password = $input['password'];

            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                exit;
            } else {
                http_response_code(403);
                echo json_encode(['error' => 'Неверный email или пароль']);
            }
        }
    }

    public function logout(): void
    {
        session_destroy();
        exit;
    }

    public function showRegisterForm(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
            $twig = new \Twig\Environment($loader);
            echo $twig->render('register.html.twig');
        }
    }
    public function showLoginForm(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
            $twig = new \Twig\Environment($loader);
            echo $twig->render('login.html.twig');
        }
    }
}