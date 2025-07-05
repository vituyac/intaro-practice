<?php

    namespace App\controllers;

    use App\services\RetailCrmService;

    class RetailCrmController {

        public function deliveryTypes(): void {
            $retailCrmService = new RetailCrmService();
            $retailCrmService->deliveryTypes();
        }

        public function paymentTypes(): void {
            $retailCrmService = new RetailCrmService();
            $retailCrmService->paymentTypes();
        }
    }

?>