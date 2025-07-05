<?php
    require __DIR__ . '/../vendor/autoload.php';

    use App\core\Router;
    use App\controllers\RetailCrmController;
    use App\controllers\OrderController;

    $router = new Router();
    $retailCrm = new RetailCrmController();
    $order = new OrderController();

    $router->get('/delivery-types', [$retailCrm, 'deliveryTypes']);
    $router->get('/payment-types', [$retailCrm, 'paymentTypes']);
    $router->post('/basket/making-an-order', [$order, 'pushOrderCrm']);

    $router->resolve();
?>