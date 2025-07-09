<?php
    namespace App\Services;

    class RetailCrmService
    {
        private string $url;
        private string $key;
        private const DELIVERY = '/api/v5/reference/delivery-types';
        private const PAYMENT = '/api/v5/reference/payment-types';
        private const ORDER = '/api/v5/orders/create';
        private const CUSTOMER_CREATE = '/api/v5/customers/create';
        private const CUSTOMER_DATA = '/api/v5/customers/';
        private const CUSTOMER_ORDERS = '/api/v5/orders';
        private const CUSTOMER_UPDATE = '/api/v5/customers/';
        private const ICML_GENERATE = '/api/v5/store/integration-module/generate';


        public function __construct()
        {
            $cfg=parse_ini_file(__DIR__.'/../../.env');
            $this->url=$cfg['RETAILCRM_API_URL'];
            $this->key=$cfg['RETAILCRM_API_KEY'];
        }

        private function request(string $api, array $payload = [], bool $post = false): array {
            $endpoint = $this->url . $api . '?apiKey=' . $this->key;
            
            $curl = curl_init($endpoint);
            $headers = ['Content-Type: ' . ($post ? 'application/x-www-form-urlencoded' : 'application/json')];
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            if ($post) {
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));
            }

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            if ($error) {
                return ['success' => false, 'message' => $error];
            }

            return json_decode($response, true);
        }


        public function getCustomerOrders(int $externalId, int $page = 1, int $limit = 10): array
        {
            $data = $this->request(self::CUSTOMER_ORDERS, [
                'customerExternalId' => $externalId,
                'page' => $page,
                'limit' => $limit
            ], false);
            
            if (!($data['success'] ?? false)) {
                return ['success' => false, 'orders' => [], 'pagination' => []];
            }
            
            return [
                'success' => true,
                'orders' => $data['orders'] ?? [],
                'pagination' => $data['pagination'] ?? []
            ];
        }

        public function updateCustomer(int $externalId, array $customerData): array
        {
            $data = $this->request(self::CUSTOMER_UPDATE . $externalId . '/edit', [
                'customer' => json_encode(array_merge(
                    ['externalId' => (string)$externalId],
                    $customerData
                ), JSON_UNESCAPED_UNICODE),
                'by' => 'externalId'
            ], true);
            
            return $data;
        }

        public function DeliveryTypes(): array {
            $data = $this->request(self::DELIVERY);
            if (!($data['success'] ?? false)){
                return ['success' => false, 'message' => 'Ошибка при получении данных из RetailCRM', 'details' => $data];
            }

            $result = [];
            foreach ($data['deliveryTypes'] as $item) {
                $result[] = [
                    'code' => $item['code'] ?? '',
                    'name' => $item['name'] ?? '',
                    'cost' => $item['defaultCost'] ?? 0
                ];
            }

            return ['success' => true, 'deliveryTypes' => $result];
        }

        public function PaymentTypes(): array {
            $data = $this->request(self::PAYMENT);
            if(!($data['success'] ?? false)) {
                return ['success' => false, 'message' => 'Ошибка при получении данных из RetailCRM', 'details' => $data];
            }

            $result = [];
            foreach ($data['paymentTypes'] as $item) {
                $result[] = [
                    'code' => $item['code'] ?? '',
                    'name' => $item['name'] ?? ''
                ];
            }
            
            return ['success' => true, 'paymentTypes' => $result];
        }

        public function createOrder(array $payload): array {
            $data = $this->request(self::ORDER, [
                'site' => $payload['site'],
                'order' => json_encode($payload['order'])
            ], true);
            
            if (($data['success'] ?? false) && isset($data['order'])) {
                $order = $data['order'];
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

            return ['success' => false, 'details' => $data];
        }

        public function registerUser(
            int $externalId,
            string $email,
            string $firstName,
            string $lastName,
            ?string $patronymic = null
        ): ?int {
            $customer = [
                'externalId' => (string)$externalId,
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'contragent' => ['contragentType' => 'individual']
            ];

            if ($patronymic) $customer['patronymic'] = $patronymic;
            $data = $this->request(self::CUSTOMER_CREATE, [
                'customer' => json_encode($customer,JSON_UNESCAPED_UNICODE),
                'site' => 'magazin-tekhniki'
            ], true);

            return (($data['success'] ?? false) && isset($data['id'])) ? (int)$data['id'] : null;
        }

        public function getCrmUser(int $externalId): array {
            $data = $this->request(self::CUSTOMER_DATA . $externalId, ['by' => 'externalId'], false);
            
            if (($data['success'] ?? false) && isset($data['customer'])) {
                $c = $data['customer'];
                return [
                    'email' => $c['email'] ?? '',
                    'firstName' => $c['firstName'] ?? '',
                    'lastName' => $c['lastName'] ?? '',
                    'patronymic' => $c['patronymic'] ?? ''
                ];
            }

            return ['success' => false, 'details' => $data];
        }
    }
?>