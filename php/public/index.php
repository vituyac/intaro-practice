<?php
    require __DIR__ . '/../vendor/autoload.php';

    use App\core\Router;
    use App\controllers\RetailCrmController;

    $router = new Router();
    $retailCrm = new RetailCrmController();

    $router->get('/delivery-types', [$retailCrm, 'deliveryTypes']);
    $router->get('/payment-types', [$retailCrm, 'paymentTypes']);

    $router->resolve();
?>