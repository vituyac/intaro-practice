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

}

