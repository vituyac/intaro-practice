<?php

namespace App\models;

use App\core\Database;
use PDO;

class Cart
{
    /**
     * @param int $userId
     * @return [{
     *  item_id, product_name, offer_id, quantity, price, 
     * }]
     */
    static function getUserCartItemList(
        int $userId
    ): array {
        $sql = "SELECT c.id as item_id, o.title as product_name, o.id as offer_id, c.quantity as quantity, c.price as price FROM cart c LEFT JOIN offers o on o.id = c.offer_id WHERE c.user_id = ?";
        $param = [$userId];
        $pdo = Database::connect();
        $query = $pdo->prepare($sql);
        $query->execute($param);
        return $query->fetchAll() ?: [];
    }


    /**
     * @param int $item_id
     * @return {
     *  item_id, product_name, offer_id, quantity, price,
     * }
     */
    static function getCartItem(
        int $item_id,
    ): array {
        $sql = "SELECT c.id as item_id, o.title as product_name, o.id as offer_id, c.quantity as quantity, c.price as price FROM cart c LEFT JOIN offers o on o.id = c.offer_id WHERE c.id = ?";
        $param = [$item_id];
        $pdo = Database::connect();
        $query = $pdo->prepare($sql);
        $query->execute($param);
        return $query->fetch() ?: [];
    }

    /**
     * @param int $userId Пользователь, для которого трогаем корзину
     * @param int $offer_id Товар, добавляемый в корзину
     * @param int $quantity Количество товара (1)
     * @param float $price Стоимость товаров
     * @return bool Результат операции
     */
    static function addCartItem(
        int $userId,
        int $offer_id,
        int $quantity = 1,
        ?float $price = null
    ) {
        $pdo = Database::connect();

        if (empty($price)) {
            $price_sql = "SELECT price FROM offers WHERE id = ?";
            $price_param = [$offer_id];
            $price_query = $pdo->prepare($price_sql);
            $price_query->execute($price_param);
            $price = $price_query->fetch()["price"];
        }

        // Проверяем, есть ли уже такой товар в корзине
        $checkSql = "SELECT id, quantity FROM cart WHERE user_id = ? AND offer_id = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$userId, $offer_id]);
        $existing = $checkStmt->fetch();

        if ($existing) {
            // Если есть — увеличиваем количество
            $updateSql = "UPDATE cart SET quantity = quantity + ? WHERE id = ?";
            $updateStmt = $pdo->prepare($updateSql);
            return $updateStmt->execute([$quantity, $existing['id']]);
        } else {
            // Если нет — обычный insert
            $sql = "INSERT INTO cart (user_id, offer_id, quantity, price) VALUES (?, ?, ?, ?)";
            $params = [$userId, $offer_id, $quantity, $price];
            return $pdo->prepare($sql)->execute($params);
        }
    }

    /**
     * @param int $item_id Id Позиции в корзине
     * @return bool Результат операции
     */
    static function removeCartItem(
        int $item_id
    ): bool {
        $pdo = Database::connect();

        $sql = "DELETE FROM cart WHERE id = ?";
        $param = [$item_id];
        return $pdo->prepare($sql)->execute($param);
    }

    /**
     * @param int $item_id
     * @param ?int $quantity
     * @param ?float $price
     * @return bool Успех операции
     */
    static function changeItem(
        int $item_id,
        ?int $quantity,
        ?float $price
    ): bool {
        if (empty($quantity) && empty($price))
            return True;
        $sql = "UPDATE cart SET ";

        $fields = [];
        $params = [];
        if (!empty($quantity)) {
            $fields[] = "quantity=?";
            $params[] = $quantity;
        }
        if (!empty($price)) {
            $fields[] = "price=?";
            $params[] = $price;
        }
        $params[] = $item_id;
        $sql .= implode(", ", $fields) . " WHERE id=?";

        $pdo = Database::connect();
        $query = $pdo->prepare($sql);
        return $query->execute($params);
    }

    /** 
     * @param int $userId Пользователь, для которого трогаем корзину
     * @return bool Результат операции
     **/
    static function clearCart(int $user_id): bool
    {
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $param = [$user_id];
        $pdo = Database::connect();
        return $pdo->prepare($sql)->execute($param);
    }
}