<?php

    namespace App\controllers;
    use App\models\Cart;
    use PDOException;

    class CartController {


        /**
         * GET: 
         * 'id' для вывода конкретного пункта корзины
         * При отсутсвии id выводит все содержимое корзины
         * 
         * @return [
         * {
         *  itemId, 
         *  productName, 
         *  offerId, 
         *  quantity, 
         *  offerPrice, 
         * },...]
         */
        function getCartItem(){
            header('Content-type: application/json');

            $id = (int)$_GET["id"] ?? null;

            // TODO: Change User id key 'userId' to actual key
            if (empty($_SESSION["userId"])){
                http_response_code(403);
                return;
            }

            try {
                if (empty($id)){
                    $data = Cart::getUserCartItemList($_SESSION["userId"]);
                } else {
                    $data = json_encode(Cart::getCartItem($id));
                }
                http_response_code(200);
                return json_encode($data);
            } catch (PDOException $e){
                http_response_code(500);
                return json_encode(["error" => $e->getMessage()]);
            }
        }

        /**
         * POST:
         * offerId - лот продажи в магазине (или что это я хз)
         * ?quantity - количество единиц товара (по умолчанию 1)
         * ?offerPrice - стоимость товара (по умолчанию берется стоимость соотв. лота)
         */
        function addCartItem(){
            header('Content-type: application/json');

            // TODO: Change User id key 'userId' to actual key
            if (empty($_SESSION["userId"])){
                http_response_code(403);
                return json_encode(["error" => "Unathorized"]);
            }

            $offerId = $_POST["offerId"] ?? null;
            if (empty($offerId) || $offerId < 0){
                http_response_code(400);
                return json_encode(["error" => "'offerId' not provided or invalid"]);
            }

            $quantity = $_POST["quantity"] ?? 1;
            $offerPrice = $_POST["offerPrice"] ?? null;
            try {
                // TODO: Change User id key 'userId' to actual key
                $res = Cart::addCartItem($_SESSION["userId"], $offerId, $quantity, $offerPrice);
                if ($res){
                    http_response_code(200);
                    return json_encode(["message" => "Successfully created new cart item"]);
                } else {
                    http_response_code(400);
                    return json_encode([
                        "error"=> "Error when creating new cart item",
                        "data" => [
                            "offerId" => $offerId,
                            "offerPrice" => $offerPrice,
                            "quantity" => $quantity
                        ]
                    ]);
                }
            } catch (PDOException $e){
                http_response_code(500);
                return json_encode(["error" => $e->getMessage()]);
            }
        }

        /**
         * GET:
         * id - id конкретного предмета в списке (itemId)
         * POST:
         * offerId - лот продажи в магазине (или что это я хз)
         * ?quantity - количество единиц товара
         * ?offerPrice - стоимость товара
         */
        function changeCartItem() {
            header('Content-type: application/json');

            $id = $_GET["id"] ?? null;
            if (empty($id) || (int)$id < 0){
                http_response_code(400);
                echo json_encode(["error" => "'id' not provided or invalid"]);
                return;
            }

            $offerPrice = $_POST["offerPrice"] ?? null;
            $quantity = $_POST["quantity"] ?? null;
            if (empty($offerPrice) && empty($quantity)){
                http_response_code(200);
                return json_encode(["message" => "Successfull update (done nothing)"]);
            }

            if (!empty($offerPrice) && $offerPrice < 0){
                http_response_code(400);
                echo json_encode(["error" => "Malformed data: 'offerPrice'"]);
            }

            if (!empty($quantity) && $quantity < 0){
                http_response_code(400);
                echo json_encode(["error" => "Malformed data: 'quantity'"]);
            }

            try {
                $res = Cart::changeItem($id, $quantity, $offerPrice);
                if ($res){
                    http_response_code(200);
                    return json_encode(["message" => "Successfully updated cart item"]);
                } else {
                    http_response_code(400);
                    return json_encode([
                        "error" => "Error when updating cart item", 
                        "data" => [
                            "itemId" => $id,
                            "offerPrice" => $offerPrice,
                            "quantity" => $quantity
                        ]
                    ]);
                }
            } catch (PDOException $e){
                http_response_code(500);
                return json_encode(["error" => $e->getMessage()]);
            }
        }

        /**
         * GET:
         * id - id конкретного предмета в списке (itemId)
         **/
        function removeCartItem() {
            header('Content-type: application/json');

            $id = $_GET['id'] ?? null;
            if (empty($id) || (int)$id < 0){
                http_response_code(400);
                echo json_encode(["error" => "'id' not provided or invalid"]);
                return;
            }

            try {
                Cart::removeCartItem($id);
            } catch (PDOException $e){
                http_response_code(500);
                return json_encode(["error" => $e->getMessage()]);
            }

            http_response_code(200);
        }
    }
?>