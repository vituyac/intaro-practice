<?php

namespace App\models;

use App\core\Database;
use PDO;

class Cart {
    /**
     * @param int $userId
     * @return [{
     *  itemId, productName, offerId, quantity, offerPrice, 
     * }]
     */
    static function getUserCartItemList(
        int $userId
    ): array {
        $sql = "SELECT c.id as itemId, p.name as productName, o.id as offerId, c.quantity as quantity, c.price as offerPrice FROM cart c LEFT JOIN offer o on o.id = c.offer_id LEFT JOIN product p on p.id = o.product_id WHERE c.user_id = ?";
        $param = [$userId];
        $pdo = Database::connect();
        $query = $pdo->prepare($sql);
        $query->execute($param); 
        return $query->fetchAll();
    }


    /**
     * @param int $itemId
     * @return {
     *  itemId, productName, offerId, quantity, offerPrice,
     * }
     */
    static function getCartItem(
        int $itemId,
    ): array {
        $sql = "SELECT c.id as itemId, p.name as productName, o.id as offerId, c.quantity as quantity, c.price as offerPrice FROM cart c LEFT JOIN offer o on o.id = c.offer_id LEFT JOIN product p on p.id = o.product_id WHERE c.id = ?";
        $param = [$itemId];
        $pdo = Database::connect();
        $query = $pdo->prepare($sql);
        $query->execute($param);
        return $query->fetch();
    }

    /**
     * @param int $userId Пользователь, для которого трогаем корзину
     * @param int $offerId Товар, добавляемый в корзину
     * @param int $quantity Количество товара (1)
     * @param float $price Стоимость товаров
     * @return bool Результат операции
     */
    static function addCartItem(
        int $userId,
        int $offerId,
        int $quantity = 1,
        ?float $price = null
    ): bool {
        $pdo = Database::connect();

        if (empty($price)){
            $price_sql = "SELECT price FROM offer WHERE id = ?";
            $price_param = [$offerId];
            $price_query = $pdo->prepare($price_sql);
            $price_query->execute($price_param);
            $price = $price_query->fetch()["price"];
        }

        $sql = "INSERT INTO cart (user_id, offer_id, quantity, price) VALUES (?, ?, ?, ?)";
        $params = [$userId, $offerId, $quantity, $price];
        return $pdo->prepare($sql )->execute($params);
    }

    /**
     * @param int $itemId Id Позиции в корзине
     * @return bool Результат операции
     */
    static function removeCartItem(
        int $itemId
    ): bool{
        $pdo = Database::connect();

        $sql = "DELETE FROM cart WHERE id = ?";
        $param = [$itemId];
        return $pdo->prepare($sql)->execute($param);
    }

    /**
     * @param int $itemId
     * @param ?int $quantity
     * @param ?float $price
     * @return bool Успех операции
     */
    static function changeItem(
        int $itemId,
        ?int $quantity,
        ?float $price
    ): bool{
        if (empty($quantity) && empty($price)) return True;
        $sql = "UPDATE cart SET ";
        
        $fields = [];
        $params = [];
        if (!empty($quantity)){
            $fields[] = "quantity=?";
            $params[] = $quantity;
        }
        if (!empty($price)){
            $fields[] = "price=?";
            $params[] = $price;
        }
        $params[]=$itemId;
        $sql .= implode(", ", $fields) . " WHERE id=?";

        $pdo = Database::connect();
        $query = $pdo->prepare($sql);
        return $query->execute($params);
    }
}