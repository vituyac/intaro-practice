<?php

namespace App\models;

use App\core\Database;

class Offer {
    private $id;
    private $product_id;
    private $title;
    private $image;
    private $price;
    private $color;
    private $discount;
    private $is_popular;
    private $is_on_sale;
    private $db;

    public function __construct($id = null, $product_id = null, $title = null, $image = null, $price = null, $color = null, $discount = null, $is_popular = null, $is_on_sale = null) {
        $this->id = $id;
        $this->product_id = $product_id;
        $this->title = $title;
        $this->image = $image;
        $this->price = $price;
        $this->color = $color;
        $this->discount = $discount;
        $this->is_popular = $is_popular;
        $this->is_on_sale = $is_on_sale;
        $this->db = Database::connect();
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getProductId() {
        return $this->product_id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getImage() {
        return $this->image;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getColor() {
        return $this->color;
    }

    public function getDiscount() {
        return $this->discount;
    }

    public function getIsPopular() {
        return $this->is_popular;
    }

    public function getIsOnSale() {
        return $this->is_on_sale;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setProductId($product_id) {
        $this->product_id = $product_id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function setColor($color) {
        $this->color = $color;
    }

    public function setDiscount($discount) {
        $this->discount = $discount;
    }

    public function setIsPopular($is_popular) {
        $this->is_popular = $is_popular;
    }

    public function setIsOnSale($is_on_sale) {
        $this->is_on_sale = $is_on_sale;
    }
} 