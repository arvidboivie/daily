<?php

require 'vendor/autoload.php';

$clientId = '***REMOVED***';
$clientSecret = '***REMOVED***';
$redirect_uri = 'https://www.arvidboivie.se/daily-double/spotify.php';

$session = new SpotifyWebAPI\Session($clientId, $clientSecret, $redirect_uri);
$api = new SpotifyWebAPI\SpotifyWebAPI();

// Request a access token using the code from Spotify
$session->requestAccessToken($_GET['code']);
$accessToken = $session->getAccessToken();

// Set the access token on the API wrapper
$api->setAccessToken($accessToken);

// Start using the API!
$playlists = $api->getUserPlaylists('arvid.b', ['limit' => 50]);

$searchList = $api->search('Dagens+Låt+**+', 'playlist');

header('Content-Type: application/json');
print_r($searchList);

// TODO: Get all daily playlists
$playlistUrl = $baseUrl + 'users/arvid.b/playlists';

// TODO: Get song title

// TODO: Get songs for each playlist in turn, and check them for duplicates
