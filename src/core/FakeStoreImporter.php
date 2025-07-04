<?php
// Импортер для FakeStoreAPI

namespace App\core;

use App\core\Database;

class FakeStoreImporter {
      private $db;
      private $apiUrl = 'https://fakestoreapi.in/api/products';
      private $categoriesUrl = 'https://fakestoreapi.in/api/products/category';
    
      public function __construct() {
            $this->db = Database::connect();
      }

      public function importAll() {
            //Импорт категорий
            $this->importCategories();
            
            //Импорт продуктов и торговых предложений
            //$this->importProducts();
      }

      private function importCategories() {  
            $json = file_get_contents($this->categoriesUrl);
            $data = json_decode($json, true);
            $categories = $data['categories'] ?? [];

            
            // Проверяем наличие родительской категории "Электроника"
            $stmt = $this->db->prepare("SELECT id FROM Section WHERE title = ?");
            $stmt->execute(['Electronics']);
            $parent = $stmt->fetch();

            if (!$parent) {
                  // Вставляем, если её нет
                  $stmt = $this->db->prepare("INSERT INTO Section (title, parent_id) VALUES (?, NULL)");
                  $stmt->execute(['Electronics']);
                  $parentId = $this->db->lastInsertId();
            } else {
                  $parentId = $parent['id'];
            }

            foreach ($categories as $categoryName) {
                  var_dump($categoryName);
                  // Проверяем, существует ли уже категория
                  $stmt = $this->db->prepare("SELECT id FROM Section WHERE title = ?");
                  $stmt->execute([ucfirst($categoryName)]);
                  $existing = $stmt->fetch();
                  
                  if (!$existing) {
                  $stmt = $this->db->prepare(
                        "INSERT INTO Section (title, parent_id) VALUES (?, ?)"
                  );
                  $stmt->execute([ucfirst($categoryName),$parentId]);
                  }
            }
      }
    

}

