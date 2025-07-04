<?php

namespace App\models;

use App\core\Database;

class Product {
    private $id;
    private $name;
    private $brand;
    private $model;
    private $description;
    private $category_id;
    private $db;

    public function __construct($id = null, $name = null, $brand = null, $model = null, $description = null, $category_id = null) {
        $this->id = $id;
        $this->name = $name;
        $this->brand = $brand;
        $this->model = $model;
        $this->description = $description;
        $this->category_id = $category_id;
        $this->db = Database::connect();
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getBrand() {
        return $this->brand;
    }

    public function getModel() {
        return $this->model;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCategoryId() {
        return $this->category_id;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setBrand($brand) {
        $this->brand = $brand;
    }

    public function setModel($model) {
        $this->model = $model;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setCategoryId($category_id) {
        $this->category_id = $category_id;
    }
} 