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

session_start();

$router = new Router();
$retailCrm = new RetailCrmController();
$order = new OrderController();
$users = new UserController();
$sections = new SectionController();
$offers = new OfferController();
$cart = new CartController();
$mocker = new MockController();

$router->get('/api/delivery-types', [$retailCrm, 'deliveryTypes']);
$router->get('/api/payment-types', [$retailCrm, 'paymentTypes']);
$router->post('/api/cart/making-an-order', [$order, 'pushOrderCrm']);

$router->post('/api/register', [$users, 'register']);
$router->post('/api/login', [$users, 'login']);
$router->get('/api/logout', [$users, 'logout']);

$router->get('/section', [$sections, 'showSection']);
$router->get('/offer', [$offers, 'showOffer']);

$router->get('/api/cart', [$cart, 'getCartItem']);
$router->post('/api/cart', [$cart, 'addCartItem']);
$router->put('/api/cart', [$cart, 'changeCartItem']);
$router->delete('/api/cart', [$cart, 'removeCartItem']);
    $router->delete('/api/clear-cart', [$cart, 'clearCart']);

$router->get('/mock/login', [$mocker, 'mockLogin']);
$router->get('/mock/check-login', [$mocker, 'checkUser']);

$router->resolve();
?>
