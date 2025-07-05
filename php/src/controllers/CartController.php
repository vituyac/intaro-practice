<?php

    namespace App\controllers;

    class CartController {


        /**
         * Summary of getCartItems
         * 
         * @return 403
         * @return 
         */
        function getCartItems(){
            header('Content-type: application/json');
            if ($_SERVER['REQUEST_METHOD'] != "GET"){    
                http_response_code(403);
                return;
            }
            
            $data = [];
        }

        function addCartItem(){

        }

        function changeItemQuantity() {

        }

        function removeCartItem() {

        }
    }
?>