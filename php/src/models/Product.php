<?php

namespace App\models;

use App\core\Database;
use PDO;

class Product {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM product ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM product WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getByBrand(string $brand): array {
        $stmt = $this->pdo->prepare("SELECT * FROM product WHERE brand = :brand ORDER BY model");
        $stmt->execute(['brand' => $brand]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory(int $categoryId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM product WHERE category_id = :category_id ORDER BY brand, model");
        $stmt->execute(['category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByBrandAndModel(string $brand, string $model): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM product WHERE brand = :brand AND model = :model");
        $stmt->execute([
            'brand' => $brand,
            'model' => $model
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function searchByName(string $searchTerm): array {
        $stmt = $this->pdo->prepare("SELECT * FROM product WHERE name ILIKE :search_term ORDER BY brand, model");
        $stmt->execute(['search_term' => "%{$searchTerm}%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct(string $name, string $brand, string $model, ?string $description, int $categoryId): int {
        $stmt = $this->pdo->prepare("INSERT INTO product (name, brand, model, description, category_id) VALUES (:name, :brand, :model, :description, :category_id) RETURNING id");
        $stmt->execute([
            'name' => htmlspecialchars($name),
            'brand' => htmlspecialchars($brand),
            'model' => htmlspecialchars($model),
            'description' => $description ? htmlspecialchars($description) : null,
            'category_id' => $categoryId
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }

    public function updateProduct(int $id, string $name, string $brand, string $model, ?string $description, int $categoryId): bool {
        $stmt = $this->pdo->prepare("UPDATE product SET name = :name, brand = :brand, model = :model, description = :description, category_id = :category_id WHERE id = :id");
        return $stmt->execute([
            'name' => htmlspecialchars($name),
            'brand' => htmlspecialchars($brand),
            'model' => htmlspecialchars($model),
            'description' => $description ? htmlspecialchars($description) : null,
            'category_id' => $categoryId,
            'id' => $id
        ]);
    }

    public function deleteProduct(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM product WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getProductsWithCategory(): array {
        $stmt = $this->pdo->query("
            SELECT p.*, s.title as category_title 
            FROM product p 
            LEFT JOIN section s ON p.category_id = s.id 
            ORDER BY p.brand, p.model
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBrands(): array {
        $stmt = $this->pdo->query("SELECT DISTINCT brand FROM product ORDER BY brand");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
} 