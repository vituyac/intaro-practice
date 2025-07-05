<?php
namespace App\services;

class RetailCrmService
{
    private string $deliveryAPI = '/api/v5/reference/delivery-types';
    private string $paymentAPI = '/api/v5/reference/payment-types';

    private function getCrmData(string $api): array
    {
        $config = parse_ini_file(__DIR__ . '/../../.env');
        $urlCrm = $config['RETAILCRM_API_URL'];
        $apiKey = $config['RETAILCRM_API_KEY'];
        $endpoint = "{$urlCrm}{$api}?apiKey={$apiKey}";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['success' => false, 'message' => 'Ошибка при получении данных из RetailCRM:' . PHP_EOL . $error];
        }

        $data = json_decode($response, true);

        if (($data['success'] ?? false) === true) {
            return $data['deliveryTypes'] ?? $data['paymentTypes'] ?? [];
        }

        return ['success' => false, 'message' => 'Непредвиденная ошибка при получении данных из RetailCRM'];
    }

    public function deliveryTypes(): void
    {
        $data = $this->getCrmData($this->deliveryAPI);
        print_r($data);
    }

    public function paymentTypes(): void
    {
        $data = $this->getCrmData($this->paymentAPI);
        print_r($data);
    }

    public static function createRetailCrmOrder(array $data): array
    {
        $config = parse_ini_file(__DIR__ . '/../../.env');
        $urlCrm = $config['RETAILCRM_API_URL'];
        $apiKey = $config['RETAILCRM_API_KEY'];
        $endpoint = "{$urlCrm}/api/v5/orders/create?apiKey={$apiKey}";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS => http_build_query([
                'site' => $data['site'],
                'order' => json_encode($data['order'])
            ])
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['success' => false, 'message' => 'Ошибка соединения с RetailCRM:' . PHP_EOL . $error];
        }

        $result = json_decode($response, true);

        if ($httpCode === 201 && ($result['success'] ?? false) === true) {
            $order = $result['order'];
            $items = [];

            foreach ($order['items'] as $item) {
                $items[] = [
                    'id' => $item['id'],
                    'name' => $item['offer']['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['initialPrice'],
                    'discount' => $item['discountTotal']
                ];
            }

            return [
                'success' => true,
                'orderId' => $order['id'],
                'totalSumm' => $order['totalSumm'],
                'currency' => $order['currency'],
                'items' => $items
            ];
        }

        return ['success' => false, 'details' => $result];
    }

    public function registerUser(
        int $externalId,
        string $email,
        string $firstName,
        string $lastName,
        ?string $patronymic = null
    ): ?int {
        $config = parse_ini_file(__DIR__ . '/../../.env');
        $urlCrm = $config['RETAILCRM_API_URL'];
        $apiKey = $config['RETAILCRM_API_KEY'];
        $endpoint = "{$urlCrm}/api/v5/customers/create?apiKey={$apiKey}";

        $customer = [
            'externalId' => (string)$externalId,
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'contragent' => ['contragentType' => 'individual']
        ];

        if ($patronymic) {
            $customer['patronymic'] = $patronymic;
        }

        $payload = [
            'customer' => json_encode($customer, JSON_UNESCAPED_UNICODE),
            'site' => 'magazin-tekhniki'
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS => http_build_query($payload)
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return null;
        }

        $data = json_decode($response, true);

        if (($data['success'] ?? false) === true && isset($data['id'])) {
            return (int)$data['id'];
        }

        return null;
    }
}
?>
