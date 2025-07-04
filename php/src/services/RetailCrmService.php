<?php

    namespace App\services;

    class RetailCrmService {

        private $deliveryAPI = '/api/v5/reference/delivery-types';
        private $paymentAPI = '/api/v5/reference/payment-types';

        private function getCrmData(string $api): array {
            $config = parse_ini_file(__DIR__ . '/../../.env');

            $urlCrm = $config['RETAILCRM_API_URL'];
            $apiKey = $config['RETAILCRM_API_KEY'];

            $endpoint = "{$urlCrm}" . $api . "?apiKey={$apiKey}";

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ]);

            $curlResponse = curl_exec($curl);
            $curlError = curl_error($curl);

            curl_close($curl);

            if ($curlError) {
                return ['success' => false, 'message' => 'Ошибка при получении данных из RetailCRM:' . PHP_EOL . $curlError];
            } else {
                $data = json_decode($curlResponse, true);
                if ($data['success'] === true) {
                    return $data['deliveryTypes'] ?? $data['paymentTypes'] ?? [];
                } else {
                    return ['success' => false, 'message' => 'Непредвиденная ошибка при получении данных из RetailCRM'];
                }
            }
        }

        public function deliveryTypes(): void {
            $data = $this->getCrmData($this->deliveryAPI);
            print_r($data);
        }

        public function paymentTypes(): void {
            $data = $this->getCrmData($this->paymentAPI);
            print_r($data);
        }

    }

?>