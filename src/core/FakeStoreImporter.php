<?php
// Импортер для FakeStoreAPI

namespace App\core;

use App\core\Database;

class FakeStoreImporter {
    private $db;
    private $apiUrl = 'https://fakestoreapi.in/api/products';
    
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

