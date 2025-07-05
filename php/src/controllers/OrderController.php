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
            $surname = trim($payload['surname']) ?? '';
            $firstname = trim($payload['firstname']) ?? '';
            $lastname = trim($payload['lastname']) ?? '';
            $email = trim($payload['email']) ?? '';
            $phone = trim($payload['phone']) ?? '';
            $address = trim($payload['address']) ?? '';
            $paymentType  = trim($payload['paymentType']) ?? '';
            $deliveryType = trim($payload['deliveryType']) ?? '';

            $paterns = PaternsFormOrder::get();

            if (!ValidationFormOrder::validate($surname, $paterns['surname']) || $surname === '') {
                $this->errors[] = 'Неверный формат фамилии';
            }
            if (!ValidationFormOrder::validate($firstname, $paterns['firstname']) || $firstname === '') {
                $this->errors[] = 'Неверный формат имени';
            }
            if (!ValidationFormOrder::validate($lastname, $paterns['lastname'])) {
                $this->errors[] = 'Неверный формат отчества';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email === '') {
                $this->errors[] = 'Неверный формат email';
            }
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
                    'status' => 'error',
                    'message' => $this->errors
                ]);
                exit;
            }

        }

    }

?>