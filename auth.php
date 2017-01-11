<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$clientId = '***REMOVED***';
$clientSecret = '***REMOVED***';
$redirect_uri = 'https://www.arvidboivie.se/daily-double/spotify.php';

$session = new SpotifyWebAPI\Session($clientId, $clientSecret, $redirect_uri);

$scopes = array(
    'playlist-read-private',
    'playlist-read-collaborative',
);

$authorizeUrl = $session->getAuthorizeUrl(array(
    'scope' => $scopes
));

header('Location: ' . $authorizeUrl);
die();
