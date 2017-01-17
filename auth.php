<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$clientId = '***REMOVED***';
$clientSecret = '***REMOVED***';
$redirect_uri = '***REMOVED***';

$session = new SpotifyWebAPI\Session($clientId, $clientSecret, $redirect_uri);

if (empty($_GET['code']) === true) {
    $scopes = array(
        'playlist-read-private',
        'playlist-read-collaborative',
    );

    $authorizeUrl = $session->getAuthorizeUrl(array(
        'scope' => $scopes
    ));

    header('Location: ' . $authorizeUrl);
    die();
}

// Request a access token using the code from Spotify
$session->requestAccessToken($_GET['code']);
$accessToken = $session->getAccessToken();
$refreshToken = $session->getRefreshToken();
$expiration = $session->getTokenExpiration();

// Create API wrapper and set access token
$api = new SpotifyWebAPI\SpotifyWebAPI();
$api->setAccessToken($accessToken);

// Start using the API!
$userInfo = $api->me();

// Store access and refresh token
$host = '***REMOVED***';
$db = '***REMOVED***';
$user = '***REMOVED***';
$password = '***REMOVED***';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$pdo = new \PDO($dsn, $user, $password);

$tokenStatement = $pdo->prepare('INSERT INTO auth(username, access_token, refresh_token, expires)
                                 VALUES(:username, :access_token, :refresh_token, :expires)
                                 ON DUPLICATE KEY UPDATE
                                 access_token= :username,
                                 refresh_token= :refresh_token,
                                 expires= :expires');

echo "time: ".time().'<br>';
echo "expire: ".$expiration;
die();

$tokenStatement->execute([
    'username' => $userInfo->id,
    'access_token' => $accessToken,
    'refresh_token' => $refreshToken,
    'expires' => time()+$expiration,
]);

echo 'Login saved';
