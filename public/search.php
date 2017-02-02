<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';

use Boivie\Spotify\Api;
use Boivie\DailyDouble\Search;

$clientId = '***REMOVED***';
$clientSecret = '***REMOVED***';
$redirectURI = '***REMOVED***';

if (empty($_GET['search']) === true) {
    header('Content-Type: application/json');
    echo json_encode('false');
}

$searchTerm = $_GET['search'];

$api = (new Api($clientId, $clientSecret, $redirectURI))->getApiWrapper();

$search = new Search($api);

$results = $search->search($searchTerm);

header('Content-Type: application/json');
echo json_encode('$results');
