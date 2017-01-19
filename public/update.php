<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';

use Boivie\Spotify\Api;
use Boivie\DailyDouble\Update;

$clientId = '***REMOVED***';
$clientSecret = '***REMOVED***';
$redirectURI = '***REMOVED***';

$api = new Api($clientId, $clientSecret, $redirectURI)->getApiWrapper();

$update = new Update($api);

$update->updatePlaylists();

echo 'Playlists updated';
