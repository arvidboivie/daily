<?php

require 'vendor/autoload.php';

use Boivie\Spotify\Api;

$clientId = '***REMOVED***';
$clientSecret = '***REMOVED***';
$redirectURI = '***REMOVED***';

$api = new Api($clientId, $clientSecret, $redirectURI);

if (empty($_GET['code']) === true) {

    $authorizeUrl = $api->getAuthorizeUrl();

    header('Location: ' . $authorizeUrl);
    die();
}
 ?>
