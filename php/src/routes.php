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

$router->get('/profile', [$profile, 'showProfile']);
$router->post('/profile', [$profile, 'updateProfile']);

$router->get('/orders', [$profile, 'showOrders']);

$router->get('/icml', function () {
    require __DIR__ . '/../public/icml.php';
});
$router->get('/', function () {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views/templates');
    $twig = new \Twig\Environment($loader);
    $user = null;
    if (isset($_SESSION['user_id'])) {
        $userModel = new \App\models\User();
        $user = $userModel->getUserById($_SESSION['user_id']);
    }
    $sectionsModel = new Section();
    $sections = $sectionsModel->getAll();
    echo $twig->render('index.html.twig', ['user' => $user, 'sections' => $sections]);
});

$router->get('/cart', [$cart, 'showCartPage']);

$router->get('/checkout', function () {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views/templates');
    $twig = new \Twig\Environment($loader);
    $user = null;
    if (isset($_SESSION['user_id'])) {
        $userModel = new \App\models\User();
        $user = $userModel->getUserById($_SESSION['user_id']);
    }
    $sectionsModel = new Section();
    $sections = $sectionsModel->getAll();

    $cartItems = [];
    if (isset($_SESSION['user_id'])) {
        $cartModel = new \App\models\Cart();
        $cartItems = $cartModel->getUserCartItemList($_SESSION['user_id']);
    }

    $cartTotal = 0;
    foreach ($cartItems as $item) {
        $cartTotal += $item['price'] * $item['quantity'];
    }

    $crmData = null;
    if ($user && isset($user['id'])) {
        $crm = new \App\Services\RetailCrmService();
        $crmData = $userModel->getCrmData($user['id'], $crm);
    }

    $crm = new \App\Services\RetailCrmService();
    $deliveryTypes = $crm->deliveryTypes();
    $paymentTypes = $crm->paymentTypes();

    echo $twig->render('checkout.html.twig', [
        'user' => $user,
        'sections' => $sections,
        'cart_items' => $cartItems,
        'cart_total' => $cartTotal,
        'crmData' => $crmData,
        'delivery_types' => $deliveryTypes,
        'payment_types' => $paymentTypes
    ]);
});

$router->get('/profile', [new \App\controllers\ProfileController($crm, $userModel, $auth), 'showProfile']);

$router->get('/profile/orders', [new \App\controllers\ProfileController($crm, $userModel, $auth), 'showOrders']);

$router->get('/register', [new \App\controllers\UserController(), 'showRegisterForm']);

$router->get('/login', [new \App\controllers\UserController(), 'showLoginForm']);

$router->get('/section', [new \App\controllers\SectionController(), 'showSectionPage']);

$router->get('/offer', [new \App\controllers\OfferController(), 'showOfferPage']);

$router->get('/order-success', function () {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views/templates');
    $twig = new \Twig\Environment($loader);
    $user = null;
    if (isset($_SESSION['user_id'])) {
        $userModel = new \App\models\User();
        $user = $userModel->getUserById($_SESSION['user_id']);
    }
    echo $twig->render('order_success.html.twig', [
        'user' => $user,
        'order_id' => $_GET['order_id'] ?? null
    ]);
});

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php')) {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views/templates');
    $twig = new \Twig\Environment($loader);
    $user = null;
    if (isset($_SESSION['user_id'])) {
        $userModel = new \App\models\User();
        $user = $userModel->getUserById($_SESSION['user_id']);
    }
    echo $twig->render('index.html.twig', ['user' => $user]);
    exit;
}
