<?php

require __DIR__.'/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Boivie\Spotify\SpotifyApi;
use Boivie\DailyDouble\Search;
use Boivie\DailyDouble\Update;
use Noodlehaus\Config;

$config = Config::load('config.yml');

$slimConfig = [
    'displayErrorDetails' => true,
    'db' => [
            'host' => $config->get('database.host'),
            'name' => $config->get('database.name'),
            'user' => $config->get('database.user'),
            'password' => $config->get('database.password'),
            'charset' => $config->get('database.charset'),
        ],
    'spotify' => $config->get('spotify'),
];

$app = new \Slim\App(['settings' => $slimConfig]);

$container = $app->getContainer();

$container['logger'] = function ($c) {
    $logger = new \Monolog\Logger('logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};
$container['db'] = function ($c) {
    $db = $c['settings']['db'];

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
            ->withHeader('Access-Control-Allow-Origin', 'https://www.arvidboivie.se')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->get('/search/{term}', function (Request $request, Response $response) {
    $spotify = $this->get('settings')['spotify'];

    $api = (new SpotifyApi(
        $this->db,
        $spotify['client_id'],
        $spotify['client_secret'],
        $spotify['redirect_URI']
    ))->getApiWrapper();

    $search = new Search($api, $this->db);

    $results = $search->getSongs($request->getAttribute('term'));

    $response->write(json_encode($results));

    return $response;
});

$app->get('/update', function (Request $request, Response $response) {
    $spotify = $this->get('settings')['spotify'];

    $api = (new SpotifyApi(
        $this->db,
        $spotify['client_id'],
        $spotify['client_secret'],
        $spotify['redirect_URI']
    ))->getApiWrapper();

    $update = new Update($api, $this->db);

    $status = $update->updatePlaylists();

    if ($status !== true) {
        $response->getBody()->write('Something went wrong');

        return $response;
    }

    $response->getBody()->write('Playlists updated');

    return $response;
});

$app->run();
