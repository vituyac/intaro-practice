<?php
namespace App\controllers;

use App\models\Section;
use App\models\Offer;

class SectionController {
    private Section $sectionModel;
    private Offer $offerModel;

    public function __construct() {
        $this->sectionModel = new Section();
        $this->offerModel = new Offer();
    }

    // GET /section?id=1&page=1&per_page=20
    // id - id раздела
    // page - номер страницы 
    // per_page - количество товаров на странице
    public function showSection(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit();
        }

        $sectionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, min(50, (int)$_GET['per_page'])) : 20;

        if ($sectionId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid section ID']);
            exit();
        }

        // Получаем информацию о разделе
        $section = $this->sectionModel->getById($sectionId);
        if (!$section) {
            http_response_code(404);
            echo json_encode(['error' => 'Section not found']);
            exit();
        }

        // Получаем дочерние разделы
        $children = $this->sectionModel->getChildren($sectionId);

        // Получаем товары раздела с пагинацией
        $offset = ($page - 1) * $perPage;
        $offers = $this->offerModel->getBySection($sectionId, $perPage, $offset);
        $totalOffers = $this->offerModel->getCountBySection($sectionId);
        $totalPages = ceil($totalOffers / $perPage);

        $response = [
            'section' => $section,
            'children' => $children,
            'offers' => $offers,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalOffers,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }
} 