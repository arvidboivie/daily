<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';

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
