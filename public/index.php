<?php

require '../vendor/autoload.php';

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use DailyDouble\Controller\SearchController;
use DailyDouble\Controller\UpdateController;
use GuzzleHttp;
use Noodlehaus\Config;

$config = Config::load('../config.yml');

$slimConfig = [
    'displayErrorDetails' => true,
];

$slimConfig = array_merge($slimConfig, $config->all());

$app = new \Slim\App(['settings' => $slimConfig]);

$container = $app->getContainer();

$container['logger'] = function ($c) {
    $logger = new \Monolog\Logger('logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};
$container['db'] = function ($c) {
    $db = $c['settings']['database'];

    $dsn = "mysql:host=".$db['host'].";dbname=".$db['name'].";charset=".$db['charset'];

    $pdo = new PDO($dsn, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// Add lazy CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader(
                'Access-Control-Allow-Origin',
                'https://www.arvidboivie.se'
            )
            ->withHeader(
                'Access-Control-Allow-Headers',
                'X-Requested-With, Content-Type, Accept, Origin, Authorization'
            )
            ->withHeader(
                'Access-Control-Allow-Methods',
                'GET, POST, PUT, DELETE, OPTIONS'
            );
});

$app->get('/search/{term}', SearchController::class . ':search');

$app->get('/update', UpdateController::class . ':update');

$app->get('/update/latest', UpdateController::class . ':updateLatest');

$app->run();
