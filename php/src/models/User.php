<?php
    namespace App\models;
    use App\core\Database;
    use App\Services\RetailCrmService;
    use PDO;

    class User {
        private PDO $pdo;

        public function __construct() {
            $this->pdo = Database::connect();
        }

        public function getUserByEmail(string $email): ?array {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch() ?: null;
        }

        public function getUserById(int $id): ?array {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch() ?: null;
        }

        public function createUser(string $email, string $password): int {
            $stmt = $this->pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
            return (int) $this->pdo->lastInsertId();
        }

        public function setExternalID(int $userId, string $externalId): void {
            $stmt = $this->pdo->prepare("UPDATE users SET external_id = ? WHERE id = ?");
            $stmt->execute([$externalId, $userId]);
        }

        public function getExternalId(int $userId): ?string {
            $stmt = $this->pdo->prepare("SELECT external_id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return $result['external_id'] ?? null;
        }

        public function getCrmData(int $userId, RetailCrmService $crmService): ?array
        {
            $externalId = $this->getExternalId($userId);
            if (!$externalId) return null;
            
            return $crmService->getCrmUser($externalId);
        }

        public function updateCrmData(int $userId, array $data, RetailCrmService $crmService): bool
        {
            $externalId = $this->getExternalId($userId);
            if (!$externalId) return false;
            
            $response = $crmService->updateCustomer($externalId, $data);
            return $response['success'] ?? false;
        }
    }
?>