<?php
require __DIR__ . '/../vendor/autoload.php';

use App\core\Router;
use App\controllers\RetailCrmController;
use App\controllers\OrderController;
use App\controllers\UserController;
use App\controllers\SectionController;
use App\controllers\OfferController;
use App\controllers\CartController;
use App\controllers\MockController;
use App\controllers\ProfileController;
use App\Services\RetailCrmService;
use App\models\User;
use App\core\Auth;
use App\models\Section;

session_start();

$router = new Router();

require __DIR__ . '/../src/routes.php';

$router->resolve();
?>