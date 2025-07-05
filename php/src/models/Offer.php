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
        $stmt = $this->pdo->query("SELECT * FROM offers ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM offers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getByProduct(int $productId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM offers WHERE product_id = :product_id ORDER BY price");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPopular(): array {
        $stmt = $this->pdo->query("SELECT * FROM offers WHERE is_popular = true ORDER BY price");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOnSale(): array {
        $stmt = $this->pdo->query("SELECT * FROM offers WHERE is_on_sale = true ORDER BY discount DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByColor(string $color): array {
        $stmt = $this->pdo->prepare("SELECT * FROM offers WHERE color = :color ORDER BY price");
        $stmt->execute(['color' => $color]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchByTitle(string $searchTerm): array {
        $stmt = $this->pdo->prepare("SELECT * FROM offers WHERE title ILIKE :search_term ORDER BY price");
        $stmt->execute(['search_term' => "%{$searchTerm}%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getColors(): array {
        $stmt = $this->pdo->query("SELECT DISTINCT color FROM offers WHERE color IS NOT NULL ORDER BY color");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getBySection(int $sectionId, int $limit = 20, int $offset = 0): array {
        $stmt = $this->pdo->prepare("
            SELECT o.* FROM offers o 
            JOIN products p ON o.product_id = p.id 
            WHERE p.category_id = :section_id 
            ORDER BY o.price 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':section_id', $sectionId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCountBySection(int $sectionId): int {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM offers o 
            JOIN products p ON o.product_id = p.id 
            WHERE p.category_id = :section_id
        ");
        $stmt->execute(['section_id' => $sectionId]);
        return (int) $stmt->fetchColumn();
    }

} 