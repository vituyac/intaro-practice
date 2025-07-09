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

session_start();

$router = new Router();
$retailCrm = new RetailCrmController();
$order = new OrderController();
$users = new UserController();
$sections = new SectionController();
$offers = new OfferController();
$cart = new CartController();
$mocker = new MockController();
$crm = new RetailCrmService();
$userModel = new User();
$auth = new Auth();
$profile = new ProfileController($crm, $userModel, $auth);

$router->get('/delivery-types', [$retailCrm, 'deliveryTypes']);
$router->get('/payment-types', [$retailCrm, 'paymentTypes']);
$router->post('/basket/making-an-order', [$order, 'pushOrderCrm']);

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

$router->get('/profile', [$profile, 'showProfile']);
$router->post('/profile', [$profile, 'updateProfile']);

$router->get('/orders', [$profile, 'showOrders']);

$router->get('/icml', function() {
    require __DIR__ . '/icml.php';
});

$router->resolve();
?>
