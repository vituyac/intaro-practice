<?php
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

$pageController = new \App\controllers\PageController();

$router->get('/api/delivery-types', [$retailCrm, 'deliveryTypes']);
$router->get('/api/payment-types', [$retailCrm, 'paymentTypes']);
$router->post('/api/cart/making-an-order', [$order, 'pushOrderCrm']);

$router->post('/api/register', [$users, 'register']);
$router->post('/api/login', [$users, 'login']);
$router->get('/api/logout', [$users, 'logout']);

$router->get('/section', [$sections, 'showSectionPage']);
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

$router->get('/icml', [$pageController, 'icml']);
$router->get('/', [$pageController, 'index']);
$router->get('/checkout', [$pageController, 'checkout']);
$router->get('/order-success', [$pageController, 'orderSuccess']);

$router->get('/cart', [$cart, 'showCartPage']);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php')) {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
    $twig = new \Twig\Environment($loader);
    $user = null;
    if (isset($_SESSION['user_id'])) {
        $userModel = new \App\models\User();
        $user = $userModel->getUserById($_SESSION['user_id']);
    }
    echo $twig->render('index.html.twig', ['user' => $user]);
    exit;
}
