<?php

namespace App\models;

use App\core\Database;
use PDO;

class Offer {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM offer ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM offer WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getByProduct(int $productId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM offer WHERE product_id = :product_id ORDER BY price");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPopular(): array {
        $stmt = $this->pdo->query("SELECT * FROM offer WHERE is_popular = true ORDER BY price");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOnSale(): array {
        $stmt = $this->pdo->query("SELECT * FROM offer WHERE is_on_sale = true ORDER BY discount DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByPriceRange(float $minPrice, float $maxPrice): array {
        $stmt = $this->pdo->prepare("SELECT * FROM offer WHERE price BETWEEN :min_price AND :max_price ORDER BY price");
        $stmt->execute([
            'min_price' => $minPrice,
            'max_price' => $maxPrice
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByColor(string $color): array {
        $stmt = $this->pdo->prepare("SELECT * FROM offer WHERE color = :color ORDER BY price");
        $stmt->execute(['color' => $color]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchByTitle(string $searchTerm): array {
        $stmt = $this->pdo->prepare("SELECT * FROM offer WHERE title ILIKE :search_term ORDER BY price");
        $stmt->execute(['search_term' => "%{$searchTerm}%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addOffer(int $productId, string $title, ?string $image, float $price, ?string $color, int $discount, bool $isPopular, bool $isOnSale): int {
        $stmt = $this->pdo->prepare("INSERT INTO offer (product_id, title, image, price, color, discount, is_popular, is_on_sale) VALUES (:product_id, :title, :image, :price, :color, :discount, :is_popular, :is_on_sale) RETURNING id");
        $stmt->execute([
            'product_id' => $productId,
            'title' => htmlspecialchars($title),
            'image' => $image,
            'price' => $price,
            'color' => $color,
            'discount' => $discount,
            'is_popular' => $isPopular ? 't' : 'f',
            'is_on_sale' => $isOnSale ? 't' : 'f'
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }

    public function updateOffer(int $id, int $productId, string $title, ?string $image, float $price, ?string $color, int $discount, bool $isPopular, bool $isOnSale): bool {
        $stmt = $this->pdo->prepare("UPDATE offer SET product_id = :product_id, title = :title, image = :image, price = :price, color = :color, discount = :discount, is_popular = :is_popular, is_on_sale = :is_on_sale WHERE id = :id");
        return $stmt->execute([
            'product_id' => $productId,
            'title' => htmlspecialchars($title),
            'image' => $image,
            'price' => $price,
            'color' => $color,
            'discount' => $discount,
            'is_popular' => $isPopular ? 't' : 'f',
            'is_on_sale' => $isOnSale ? 't' : 'f',
            'id' => $id
        ]);
    }

    public function deleteOffer(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM offer WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getOffersWithProduct(): array {
        $stmt = $this->pdo->query("
            SELECT o.*, p.name as product_name, p.brand, p.model, s.title as category_title
            FROM offer o 
            LEFT JOIN product p ON o.product_id = p.id 
            LEFT JOIN section s ON p.category_id = s.id
            ORDER BY o.price
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getColors(): array {
        $stmt = $this->pdo->query("SELECT DISTINCT color FROM offer WHERE color IS NOT NULL ORDER BY color");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPriceStats(): array {
        $stmt = $this->pdo->query("
            SELECT 
                MIN(price) as min_price,
                MAX(price) as max_price,
                AVG(price) as avg_price,
                COUNT(*) as total_offers
            FROM offer
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 