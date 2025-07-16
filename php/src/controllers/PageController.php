<?php
namespace App\controllers;

use App\models\User;
use App\models\Section;
use App\models\Cart;
use App\Services\RetailCrmService;

class PageController
{
    public function index()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
        $twig = new \Twig\Environment($loader);
        $user = null;
        if (isset($_SESSION['user_id'])) {
            $userModel = new User();
            $user = $userModel->getUserById($_SESSION['user_id']);
        }
        $sectionsModel = new Section();
        $sections = $sectionsModel->getAll();
        echo $twig->render('index.html.twig', ['user' => $user, 'sections' => $sections]);
    }

    public function checkout()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
        $twig = new \Twig\Environment($loader);
        $user = null;
        if (isset($_SESSION['user_id'])) {
            $userModel = new User();
            $user = $userModel->getUserById($_SESSION['user_id']);
        }
        $sectionsModel = new Section();
        $sections = $sectionsModel->getAll();
        $cartItems = [];
        if (isset($_SESSION['user_id'])) {
            $cartModel = new Cart();
            $cartItems = $cartModel->getUserCartItemList($_SESSION['user_id']);
        }
        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
        }
        $crmData = null;
        if ($user && isset($user['id'])) {
            $crm = new RetailCrmService();
            $crmData = $userModel->getCrmData($user['id'], $crm);
        }
        $crm = new RetailCrmService();
        $deliveryTypes = $crm->deliveryTypes();
        $paymentTypes = $crm->paymentTypes();
        echo $twig->render('checkout.html.twig', [
            'user' => $user,
            'sections' => $sections,
            'cart_items' => $cartItems,
            'cart_total' => $cartTotal,
            'crmData' => $crmData,
            'delivery_types' => $deliveryTypes,
            'payment_types' => $paymentTypes
        ]);
    }

    public function orderSuccess()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
        $twig = new \Twig\Environment($loader);
        $user = null;
        if (isset($_SESSION['user_id'])) {
            $userModel = new User();
            $user = $userModel->getUserById($_SESSION['user_id']);
        }
        echo $twig->render('order_success.html.twig', [
            'user' => $user,
            'order_id' => $_GET['order_id'] ?? null
        ]);
    }

    public function icml()
    {
        require __DIR__ . '/../../public/icml.php';
    }
}