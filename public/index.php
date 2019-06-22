<?php
error_reporting(1);
// use Psr\Http\Message\ServerRequestInterface as Request;
// use Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../server/config/db.php';

// $app = new \Slim\App;

$app = new \Slim\App;
// Customer Routers
require '../server/routes/users.php';
require '../server/routes/auth.php';
require '../server/routes/requests.php';
require '../server/routes/bills.php';
require '../server/routes/payments.php';

$app->run();

?>



