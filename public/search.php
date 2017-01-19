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

$searchTerm = empty($_GET['search']) === false ? $_GET['search'] : false;

if ($searchTerm === false) {
    echo "Please enter a search term in the query string: <br>".
         "..search.php?search='search term'";
    die();
}

$api = (new Api($clientId, $clientSecret, $redirectURI))->getApiWrapper();

$search = new Search($api);

$results = $search->search($searchTerm);

foreach ($results as $result) {
    echo '"'.$result['track_name'].' - '.$result['album'].'" in '.$result['playlist_name'].'<br>';
}
