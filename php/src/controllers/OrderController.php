<?php

    namespace App\controllers;

    use App\services\RetailCrmService;

    use App\utils\PaternsFormOrder;
    use App\utils\ValidationFormOrder;

    class OrderController {

        private $errors = [];

        public function pushOrderCrm(): void {
            $payload = json_decode(file_get_contents('php://input'), true);
            
            $userId = trim($payload['id']) ?? '';
            $phone = trim($payload['phone']) ?? '';
            $address = trim($payload['address']) ?? '';
            $paymentType  = trim($payload['paymentType']) ?? '';
            $deliveryType = trim($payload['deliveryType']) ?? '';

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
                    'items' => [
                        [
                            'productName' => 'Товар №1',
                            'initialPrice' => 6000,
                            'quantity' => 5,
                            'offer' => [
                                'externalId' => '11'
                            ]
                        ]
                    ],
                    'customer' => [
                        'externalId' => $userId
                    ]
                ]
            ];

            $response = $retailCrmService->createOrder($data);

            if ($response['success']) {
                http_response_code(201);
                echo json_encode($response);
            } else {
                http_response_code(400);
                echo json_encode($response);
            }

        }

    }

?>