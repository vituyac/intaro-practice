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
         *  item_id, 
         *  product_name, 
         *  offer_id, 
         *  quantity, 
         *  price, 
         * },...]
         */
        function getCartItem(){
            header('Content-type: application/json');

            $id = (int)($_GET["id"] ?? null);

            if (empty($_SESSION["user_id"])){
                http_response_code(403);
                return;
            }

            try {
                if (empty($id)){
                    $data = Cart::getUserCartItemList($_SESSION["user_id"]);
                } else {
                    $data = Cart::getCartItem($id);
                }
                http_response_code(200);
                echo json_encode($data);
                return;
            } catch (PDOException $e){
                http_response_code(500);
                echo json_encode(["error" => $e->getMessage()]);
                return;
            }
        }

        /**
         * POST:
         * offer_id - лот продажи в магазине (или что это я хз)
         * ?quantity - количество единиц товара (по умолчанию 1)
         * ?price - стоимость товара (по умолчанию берется стоимость соотв. лота)
         */
        function addCartItem(){
            header('Content-type: application/json');
            // МММ php просто так JSON в POST не парсит
            $_POST = json_decode(file_get_contents("php://input"), true);

            if (empty($_SESSION["user_id"])){
                http_response_code(403);
                echo json_encode(["error" => "Unathorized"]);
                return;
            }
            $offer_id = $_POST["offer_id"] ?? null;
            if (empty($offer_id) || $offer_id < 0){
                http_response_code(400);
                echo json_encode(["error" => "'offer_id' not provided or invalid"]);
                return;
            }

            $quantity = $_POST["quantity"] ?? 1;
            $price = $_POST["price"] ?? null;
            try {
                $res = Cart::addCartItem($_SESSION["user_id"], $offer_id, $quantity, $price);
                if ($res){
                    http_response_code(200);
                    echo json_encode(["message" => "Successfully created new cart item"]);
                    return;
                } else {
                    http_response_code(400);
                    echo json_encode([
                        "error"=> "Error when creating new cart item",
                        "data" => [
                            "offer_id" => $offer_id,
                            "price" => $price,
                            "quantity" => $quantity
                        ]
                    ]);
                    return;
                }
            } catch (PDOException $e){
                http_response_code(500);
                echo json_encode(["error" => $e->getMessage()]);
                return;
            }
        }

        /**
         * GET:
         * id - id конкретного предмета в списке (item_id)
         * POST:
         * ?quantity - количество единиц товара
         * ?price - стоимость товара
         */
        function changeCartItem() {
            header('Content-type: application/json');
            // МММ php просто так JSON в POST не парсит
            $_POST = json_decode(file_get_contents("php://input"), true);

            $id = $_GET["id"] ?? null;
            if (empty($id) || (int)$id < 0){
                http_response_code(400);
                echo json_encode(["error" => "'id' not provided or invalid"]);
                return;
            }

            $price = $_POST["price"] ?? null;
            $quantity = $_POST["quantity"] ?? null;
            if (empty($price) && empty($quantity)){
                http_response_code(200);
                echo json_encode(["message" => "Successfull update (done nothing)"]);
                return;
            }

            if (!empty($price) && $price < 0){
                http_response_code(400);
                echo json_encode(["error" => "Malformed data: 'price'"]);
                return;
            }

            if (!empty($quantity) && $quantity < 0){
                http_response_code(400);
                echo json_encode(["error" => "Malformed data: 'quantity'"]);
                return;
            }

            try {
                $res = Cart::changeItem($id, $quantity, $price);
                if ($res){
                    http_response_code(200);
                    echo json_encode(["message" => "Successfully updated cart item"]);
                    return;
                } else {
                    http_response_code(400);
                    echo json_encode([
                        "error" => "Error when updating cart item", 
                        "data" => [
                            "item_id" => $id,
                            "price" => $price,
                            "quantity" => $quantity
                        ]
                    ]);
                    return;
                }
            } catch (PDOException $e){
                http_response_code(500);
                echo json_encode(["error" => $e->getMessage()]);
                return;
            }
        }

        /**
         * GET:
         * id - id конкретного предмета в списке (item_id)
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
                echo json_encode(["error" => $e->getMessage()]);
                return;
            }

            http_response_code(200);
        }
    }
?>