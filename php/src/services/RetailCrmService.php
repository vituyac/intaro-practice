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

        public function __construct()
        {
            $cfg=parse_ini_file(__DIR__.'/../../.env');
            $this->url=$cfg['RETAILCRM_API_URL'];
            $this->key=$cfg['RETAILCRM_API_KEY'];
        }

        private function request(string $api,array $payload=[],bool $post=false):array
        {
            $endpoint=$this->url.$api.'?apiKey='.$this->key;
            $curl=curl_init($endpoint);
            $headers=['Content-Type: '.($post?'application/x-www-form-urlencoded':'application/json')];
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
            if($post){
                curl_setopt($curl,CURLOPT_POST,true);
                curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($payload));
            }
            $response=curl_exec($curl);
            $error=curl_error($curl);
            curl_close($curl);
            if($error)return['success'=>false,'message'=>$error];
            return json_decode($response,true);
        }

        public function getDeliveryTypes():array
        {
            $data=$this->request(self::DELIVERY);
            if(!($data['success']??false))return$data;
            $result=[];
            foreach($data['deliveryTypes']??[]as$item){
                $result[]=[
                    'code'=>$item['code']??'',
                    'name'=>$item['name']??'',
                    'cost'=>$item['defaultCost']??0
                ];
            }
            return['success'=>true,'deliveryTypes'=>$result];
        }

        public function getPaymentTypes():array
        {
            $data=$this->request(self::PAYMENT);
            if(!($data['success']??false))return$data;
            $result=[];
            foreach($data['paymentTypes']??[]as$item){
                $result[]=[
                    'code'=>$item['code']??'',
                    'name'=>$item['name']??''
                ];
            }
            return['success'=>true,'paymentTypes'=>$result];
        }

        public function createOrder(array $payload):array
        {
            $data=$this->request(self::ORDER,[
                'site'=>$payload['site'],
                'order'=>json_encode($payload['order'])
            ],true);
            if(($data['success']??false)&&isset($data['order'])){
                $order=$data['order'];
                $items=[];
                foreach($order['items']as$item){
                    $items[]=[
                        'id'=>$item['id'],
                        'name'=>$item['offer']['name'],
                        'quantity'=>$item['quantity'],
                        'price'=>$item['initialPrice'],
                        'discount'=>$item['discountTotal']
                    ];
                }
                return[
                    'success'=>true,
                    'orderId'=>$order['id'],
                    'totalSumm'=>$order['totalSumm'],
                    'currency'=>$order['currency'],
                    'items'=>$items
                ];
            }
            return['success'=>false,'details'=>$data];
        }

        public function registerUser(
            int $externalId,
            string $email,
            string $firstName,
            string $lastName,
            ?string $patronymic=null
        ):?int{
            $customer=[
                'externalId'=>(string)$externalId,
                'email'=>$email,
                'firstName'=>$firstName,
                'lastName'=>$lastName,
                'contragent'=>['contragentType'=>'individual']
            ];
            if($patronymic)$customer['patronymic']=$patronymic;
            $data=$this->request(self::CUSTOMER_CREATE, [
                'customer'=>json_encode($customer,JSON_UNESCAPED_UNICODE),
                'site'=>'magazin-tekhniki'
            ], true);
            return(($data['success']??false)&&isset($data['id']))?(int)$data['id']:null;
        }

        public function getCrmUser(int $externalId):array
        {
            $data=$this->request(self::CUSTOMER_DATA . $externalId, ['by'=>'externalId'], false);
            if(($data['success']??false)&&isset($data['customer'])){
                $c=$data['customer'];
                return[
                    'email'=>$c['email']??'',
                    'firstName'=>$c['firstName']??'',
                    'lastName'=>$c['lastName']??'',
                    'patronymic'=>$c['patronymic']??''
                ];
            }
            return['success'=>false,'details'=>$data];
        }
    }
?>