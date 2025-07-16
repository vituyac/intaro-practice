<?php
namespace App\controllers;

use App\Services\RetailCrmService;
use App\models\User;
use App\core\Auth;

class ProfileController
{
    private RetailCrmService $crm;
    private User $userModel;
    private Auth $auth;

    public function __construct(RetailCrmService $crm, User $userModel, Auth $auth)
    {
        $this->crm = $crm;
        $this->userModel = $userModel;
        $this->auth = $auth;
    }

    public function showProfile()
    {
        if (!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $userId = $this->auth->getUserId();
        $profileData = $this->userModel->getCrmData($userId, $this->crm);
        $user = $this->userModel->getUserById($userId);
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
        $twig = new \Twig\Environment($loader);
        $twig->addGlobal('user', $user);
        echo $twig->render('profile.html.twig', [
            'profileData' => $profileData,
            // можно добавить другие переменные, если нужны шаблону
        ]);
    }

    public function showOrders()
    {
        if (!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $userId = $this->auth->getUserId();
        $externalId = $this->userModel->getExternalId($userId);
        $user = $this->userModel->getUserById($userId);
        $email = $user['email'] ?? null;
        $page = max(1, $_GET['page'] ?? 1);

        $ordersData = $externalId
            ? $this->crm->getCustomerOrders($externalId, $page)
            : ['success' => false, 'orders' => [], 'pagination' => []];

        if (!empty($ordersData['orders']) && $email) {
            $ordersData['orders'] = array_filter($ordersData['orders'], function ($order) use ($email) {
                return isset($order['email']) && $order['email'] === $email;
            });
        }

        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
        $twig = new \Twig\Environment($loader);
        $twig->addGlobal('user', $user);
        echo $twig->render('orders.html.twig', [
            'orders' => $ordersData['orders'] ?? [],
            'pagination' => $ordersData['pagination'] ?? [],
        ]);
    }

    public function updateProfile()
    {
        if (!$this->auth->isLoggedIn()) {
            http_response_code(403);
            exit;
        }

        $userId = $this->auth->getUserId();

        $data = [
            'firstName' => $_POST['firstName'] ?? '',
            'lastName' => $_POST['lastName'] ?? '',
            'patronymic' => $_POST['patronymic'] ?? null,
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'birthday' => $_POST['birthday'] ?? '',
            'address' => ['text' => $_POST['address'] ?? '']
        ];
        $success = $this->userModel->updateCrmData($userId, $data, $this->crm);

        if ($success) {
            header('Location: /profile?success=1');
        } else {
            header('Location: /profile?error=1');
        }
        exit;
    }
}
?>