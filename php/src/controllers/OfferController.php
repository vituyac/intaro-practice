<?php
namespace App\controllers;

use App\models\Offer;
use App\models\Product;

class OfferController
{
    private Offer $offerModel;
    private Product $productModel;

    public function __construct()
    {
        $this->offerModel = new Offer();
        $this->productModel = new Product();
    }

    // GET /offer?id=1
    // id - id предложения
    public function showOffer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit();
        }

        $offerId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($offerId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid offer ID']);
            exit();
        }

        // Получаем информацию о предложении
        $offer = $this->offerModel->getById($offerId);
        if (!$offer) {
            http_response_code(404);
            echo json_encode(['error' => 'Offer not found']);
            exit();
        }

        // Получаем информацию о товаре
        $product = $this->productModel->getById($offer['product_id']);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            exit();
        }

        // Получаем все предложения этого товара, кроме текущего предложения
        $relatedOffers = array_filter(
            $this->offerModel->getByProduct($offer['product_id']),
            function ($relatedOffer) use ($offerId) {
                return $relatedOffer['id'] != $offerId;
            }
        );

        $response = [
            'offer' => [
                'id' => $offer['id'],
                'title' => $offer['title'],
                'image' => $offer['image'],
                'price' => $offer['price'],
                'color' => $offer['color'],
                'discount' => $offer['discount'],
                'is_popular' => $offer['is_popular'],
                'is_on_sale' => $offer['is_on_sale']
            ],
            'product' => [
                'id' => $product['id'],
                'name' => $product['name'],
                'brand' => $product['brand'],
                'model' => $product['model'],
                'description' => $product['description']
            ],
            'related_offers' => array_map(function ($relatedOffer) {
                return [
                    'id' => $relatedOffer['id'],
                    'title' => $relatedOffer['title'],
                    'image' => $relatedOffer['image'],
                    'price' => $relatedOffer['price'],
                    'color' => $relatedOffer['color'],
                    'discount' => $relatedOffer['discount']
                ];
            }, $relatedOffers)
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function showOfferPage(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $user = null;
            if (isset($_SESSION['user_id'])) {
                $userModel = new \App\models\User();
                $user = $userModel->getUserById($_SESSION['user_id']);
            }
            $offerId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $offer = $this->offerModel->getById($offerId);
            if (!$offer) {
                http_response_code(404);
                echo 'Offer not found';
                exit();
            }
            $product = $this->productModel->getById($offer['product_id']);
            $relatedOffers = array_filter(
                $this->offerModel->getByProduct($offer['product_id']),
                function ($relatedOffer) use ($offerId) {
                    return $relatedOffer['id'] != $offerId;
                }
            );
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
            $twig = new \Twig\Environment($loader);
            echo $twig->render('offer.html.twig', [
                'user' => $user,
                'offer' => $offer,
                'product' => $product,
                'related_offers' => $relatedOffers
            ]);
        }
    }
}