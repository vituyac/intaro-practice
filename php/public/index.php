<?php
    require __DIR__ . '/../vendor/autoload.php';

    use App\core\Router;
    use App\controllers\RetailCrmController;
    use App\controllers\UserController;

    $router = new Router();
    $retailCrm = new RetailCrmController();
    $users = new UserController();

    $router->get('/delivery-types', [$retailCrm, 'deliveryTypes']);
    $router->get('/payment-types', [$retailCrm, 'paymentTypes']);

    $router->post('/register', [$users, 'register']);

    $router->resolve();
?>