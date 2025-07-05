<?php
    require __DIR__ . '/../vendor/autoload.php';

    use App\controllers\CartController;
    use App\core\Router;
    use App\controllers\RetailCrmController;
    use App\controllers\UserController;
    use App\controllers\SectionController;
    use App\controllers\OfferController;

    $router = new Router();
    $retailCrm = new RetailCrmController();
    $users = new UserController();
    $sections = new SectionController();
    $offers = new OfferController();
    $cart = new CartController();

    $router->get('/delivery-types', [$retailCrm, 'deliveryTypes']);
    $router->get('/payment-types', [$retailCrm, 'paymentTypes']);

    $router->post('/register', [$users, 'register']);

    $router->get('/section', [$sections, 'showSection']);
    $router->get('/offer', [$offers, 'showOffer']);

    $router->get('/api/cart', [$cart, 'getCartItem']);
    $router->post('/api/cart', [$cart, 'addCartItem']);
    $router->put('/api/cart', [$cart, 'changeCartItem']);
    $router->delete('/api/cart', [$cart, 'removeCartItem']);

    $router->resolve();
?>