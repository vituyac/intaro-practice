<?php
namespace App\controllers;

use App\models\User;
use App\models\Section;
use App\models\Cart;
use App\Services\RetailCrmService;

class PageController
{
    public function index()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
        $twig = new \Twig\Environment($loader);
        $user = null;
        if (isset($_SESSION['user_id'])) {
            $userModel = new User();
            $user = $userModel->getUserById($_SESSION['user_id']);
        }
        $sectionsModel = new Section();
        $sections = $sectionsModel->getAll();
        echo $twig->render('index.html.twig', ['user' => $user, 'sections' => $sections]);
    }

    public function icml()
    {
        require __DIR__ . '/../../public/icml.php';
    }
}