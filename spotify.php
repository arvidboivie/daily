<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$clientId = '***REMOVED***';
$clientSecret = '***REMOVED***';
$redirect_uri = 'https://www.arvidboivie.se/daily-double/spotify.php';

$userId = 'arvid.b';

$session = new SpotifyWebAPI\Session($clientId, $clientSecret, $redirect_uri);
$api = new SpotifyWebAPI\SpotifyWebAPI();

// Request a access token using the code from Spotify
$session->requestAccessToken($_GET['code']);
$accessToken = $session->getAccessToken();

// Set the access token on the API wrapper
$api->setAccessToken($accessToken);

// Start using the API!
$playlists = $api->getUserPlaylists($userId, ['limit' => 50]);

$playlists = array_filter($playlists->items, function($list) {
    if (preg_match('/Dagens Låt \d{2}/', $list->name) === 1) {
        return true;
    }
});

$searchTerm = 'get';

$songs = [];

foreach ($playlists as $list) {
    $songs = array_merge($songs, $api->getUserPlaylistTracks($list->owner->id, $list->id)->items);
}

$results = array_filter($songs, function($song) use ($searchTerm) {
    if (preg_match('/'.$searchTerm.'/', $song->name) === 1) {
        return true;
    }
    if (preg_match('/'.$searchTerm.'/', $song->album->name) === 1) {
        return true;
    }
});

foreach ($results as $result) {
    echo $result->name.' - '.$result->album->name.'<br>';
}

// TODO: Get all daily playlists
// $playlistUrl = $baseUrl . 'users/arvid.b/playlists';

// TODO: Get song title

// TODO: Get songs for each playlist in turn, and check them for duplicates
