<?php

namespace App\controllers;

use App\services\RetailCrmService;
use App\utils\PaternsFormOrder;
use App\utils\ValidationFormOrder;
use App\models\Cart;

class OrderController
{

    private $errors = [];

    public function pushOrderCrm(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        $userId = trim($payload['id'] ?? '');
        $phone = trim($payload['phone'] ?? '');
        $address = trim($payload['address'] ?? '');
        $paymentType = trim($payload['paymentType'] ?? '');
        $deliveryType = trim($payload['deliveryType'] ?? '');

        $retailCrmService = new RetailCrmService();
        $userData = $retailCrmService->getCrmUser($userId);

        $firstName = $userData['firstName'];
        $lastName = $userData['lastName'];
        $patronymic = $userData['patronymic'];
        $email = $userData['email'];

        $paterns = PaternsFormOrder::get();

        if (!ValidationFormOrder::validate($phone, $paterns['phone']) || $phone === '') {
            $this->errors[] = 'Неверный формат телефона';
        }
        if (!ValidationFormOrder::validate($address, $paterns['address']) || $address === '') {
            $this->errors[] = 'Неверный формат адреса';
        }
        if ($paymentType === '') {
            $this->errors[] = 'Не указан тип оплаты';
        }
        if ($deliveryType === '') {
            $this->errors[] = 'Не указан тип доставки';
        }

        if (!empty($this->errors)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => $this->errors
            ]);
            exit;
        }

        $cartItems = Cart::getUserCartItemList($userId);
        if (empty($cartItems)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Корзина пуста'
            ]);
            exit;
        }

        $orderItems = [];
        foreach ($cartItems as $item) {
            $orderItems[] = [
                'productName' => $item['product_name'],
                'initialPrice' => (float) $item['price'],
                'quantity' => (int) $item['quantity'],
                'offer' => [
                    'externalId' => (string) $item['offer_id']
                ]
            ];
        }

        $data = [
            'site' => 'magazin-tekhniki',
            'order' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'patronymic' => $patronymic,
                'phone' => $phone,
                'email' => $email,
                'orderMethod' => 'shopping-cart',
                'orderType' => 'eshop-individual',
                'isFromCart' => true,
                'delivery' => [
                    'code' => $deliveryType,
                    'address' => [
                        'text' => $address
                    ]
                ],
                'payments' => [
                    [
                        'type' => $paymentType,
                        'status' => 'invoice'
                    ]
                ],
                'items' => $orderItems,
                'customer' => [
                    'externalId' => $userId
                ],
                'contragent' => [
                    'contragentType' => 'individual'
                ]
            ]
        ];

        $response = $retailCrmService->createOrder($data);

        if ($response['success']) {
            Cart::clearCart($userId);
            http_response_code(201);
            echo json_encode($response);
        } else {
            http_response_code(400);
            echo json_encode($response);
        }

    }

    public function showCheckoutPage(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $user = null;
            if (isset($_SESSION['user_id'])) {
                $userModel = new \App\models\User();
                $user = $userModel->getUserById($_SESSION['user_id']);
            }
            if (empty($_SESSION["user_id"])) {
                header('Location: /login');
                exit();
            }
            $cartItems = \App\models\Cart::getUserCartItemList($_SESSION["user_id"]);
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
            $twig = new \Twig\Environment($loader);
            echo $twig->render('checkout.html.twig', [
                'user' => $user,
                'cartItems' => $cartItems
            ]);
        }
    }

}

?>