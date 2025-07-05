<?php

namespace App\services;

use App\core\Database;

class FakeStoreImporter {
      private $db;
      private $apiUrl = 'https://fakestoreapi.in/api/products?limit=150';
      private $categoriesUrl = 'https://fakestoreapi.in/api/products/category';
    
      public function __construct() {
            $this->db = Database::connect();
      }

      public function importAll() {
            //Импорт категорий
            $this->importCategories();
            
            //Импорт продуктов и торговых предложений
            $this->importProducts();
      }

      private function importCategories() {  
            $json = file_get_contents($this->categoriesUrl);
            $data = json_decode($json, true);
            $categories = $data['categories'] ?? [];

            
            // Проверяем наличие родительской категории Электроника
            $stmt = $this->db->prepare("SELECT id FROM sections WHERE title = ?");
            $stmt->execute(['Electronics']);
            $parent = $stmt->fetch();

            if (!$parent) {
                  $stmt = $this->db->prepare("INSERT INTO sections (title, parent_id) VALUES (?, NULL)");
                  $stmt->execute(['Electronics']);
                  $parentId = $this->db->lastInsertId();
            } else {
                  $parentId = $parent['id'];
            }

            foreach ($categories as $categoryName) {
                  var_dump($categoryName);
                  $stmt = $this->db->prepare("SELECT id FROM sections WHERE title = ?");
                  $stmt->execute([ucfirst($categoryName)]);
                  $existing = $stmt->fetch();
                  
                  if (!$existing) {
                  $stmt = $this->db->prepare(
                        "INSERT INTO sections (title, parent_id) VALUES (?, ?)"
                  );
                  $stmt->execute([ucfirst($categoryName),$parentId]);
                  }
            }
      }
      
      
      private function importProducts() {
            $json = file_get_contents($this->apiUrl);
            $data = json_decode($json, true);
            $products = $data['products'] ?? [];

            foreach ($products as $item) {
                  $brand = ucfirst(trim($item['brand']));
                  $model = trim($item['model']);
                  $name = trim($item['title']);
                  $description = $item['description'] ?? '';
                  $categoryTitle = ucfirst($item['category']);

                  //ID категории
                  $stmt = $this->db->prepare("SELECT id FROM sections WHERE title = ?");
                  $stmt->execute([$categoryTitle]);
                  $category = $stmt->fetch();

                  if (!$category) {
                        echo "Категория '$categoryTitle' не найдена. Пропускаем товар.\n";
                        continue;
                  }
                  $categoryId = $category['id'];

                  // наличие товара
                  $stmt = $this->db->prepare("SELECT id FROM products WHERE brand = ? AND model = ?");
                  $stmt->execute([$brand, $model]);
                  $product = $stmt->fetch();

                  if (!$product) {
                        $stmt = $this->db->prepare("
                        INSERT INTO products (name, brand, model, description, category_id)
                        VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([$name, $brand, $model, $description, $categoryId]);
                        $productId = $this->db->lastInsertId();
                  } else {
                        $productId = $product['id'];
                  }
 
                  // торговое предложение                  
                  $isPopular = $this->normalizeBoolean($item['popular'] ?? false);
                  $isOnSale = $this->normalizeBoolean($item['onSale'] ?? false);

                  $stmt = $this->db->prepare("
                        INSERT INTO offers (
                        product_id, title, image, price, color, discount,
                        is_popular, is_on_sale
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                  ");

                  $stmt->execute([
                        $productId,
                        $item['title'],
                        $item['image'] ?? null,
                        $item['price'],
                        $item['color'] ?? null,
                        $item['discount'] ?? 0,
                        $isPopular ? 't' : 'f', 
                        $isOnSale ? 't' : 'f'
                  ]);
            }
      }

      private function normalizeBoolean($value): bool
      {
            if (is_bool($value)) {
                  return $value;
            }
            if (is_int($value)) {
                  return $value === 1;
            }
            if (is_string($value)) {
                  $value = strtolower(trim($value));
                  if (empty($value)) {
                        return false;
                  }
                  return in_array($value, ['true', '1', 'yes', 'y', 'on', 't']);
            }
            return false;
      }
}

