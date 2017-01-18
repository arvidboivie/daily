<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$clientId = '***REMOVED***';
$clientSecret = '***REMOVED***';

$userId = 'arvid.b';

$session = new SpotifyWebAPI\Session($clientId, $clientSecret);
$api = new SpotifyWebAPI\SpotifyWebAPI();

// Store access and refresh token
$host = '***REMOVED***';
$db = '***REMOVED***';
$user = '***REMOVED***';
$password = '***REMOVED***';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$pdo = new \PDO($dsn, $user, $password);

$tokenStatement = $pdo->prepare("SELECT access_token, refresh_token, expires
                                 FROM `auth`
                                 WHERE username = 'arvid.b'");

$tokenStatement->execute();

$result = $tokenStatement->fetchObject();

$accessToken = $result->access_token;

if (time() > $result->expires) {
    $session->refreshAccessToken($result->refresh_token);
    $accessToken = $session->getAccessToken();
}

// Set the access token on the API wrapper
$api->setAccessToken($accessToken);

// Start using the API!

$playlists = $api->getUserPlaylists($userId, ['limit' => 50]);

$playlists = array_filter($playlists->items, function($list) {
    if (preg_match('/Dagens Låt \d{2}/', $list->name) === 1) {
        return true;
    }
});

$searchTerm = empty($_GET['search']) === false ? $_GET['search'] : 'love';

echo 'Search term: '.$searchTerm.'<br><br>';

$songs = [];

foreach ($playlists as $list) {
    $songs = array_merge($songs, $api->getUserPlaylistTracks($list->owner->id, $list->id)->items);
}

$results = array_filter($songs, function($songObject) use ($searchTerm) {
    if (preg_match('/'.$searchTerm.'/i', $songObject->track->name) === 1) {
        return true;
    }
    if (preg_match('/'.$searchTerm.'/i', $songObject->track->album->name) === 1) {
        return true;
    }
});

foreach ($results as $result) {
    echo $result->track->name.' - '.$result->track->album->name.'<br>';
}

// TODO: Get all daily playlists
// $playlistUrl = $baseUrl . 'users/arvid.b/playlists';

// TODO: Get song title

// TODO: Get songs for each playlist in turn, and check them for duplicates
