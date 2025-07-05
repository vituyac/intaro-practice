<?php

namespace App\models;

use App\core\Database;
use PDO;

class Section {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM sections ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM sections WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getByTitle(string $title): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM sections WHERE title = :title");
        $stmt->execute(['title' => $title]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getChildren(int $parentId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM sections WHERE parent_id = :parent_id ORDER BY title");
        $stmt->execute(['parent_id' => $parentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRootSections(): array {
        $stmt = $this->pdo->query("SELECT * FROM sections WHERE parent_id IS NULL ORDER BY title");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 