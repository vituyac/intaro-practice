<?php

    namespace App\controllers;

    use App\services\RetailCrmService;

    class RetailCrmController {

        public function deliveryTypes(): void {
            $retailCrmService = new RetailCrmService();
            $data = $retailCrmService->deliveryTypes();
            echo json_encode($data);
        }

        public function paymentTypes(): void {
            $retailCrmService = new RetailCrmService();
            $data = $retailCrmService->paymentTypes();
            echo json_encode($data);
        }
    }

?>