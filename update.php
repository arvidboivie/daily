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

$playlistStatement = $pdo->prepare('INSERT INTO playlists(id, name)
                                    VALUES(:id, :name)
                                    ON DUPLICATE KEY UPDATE
                                    name= :name');

$trackStatement = $pdo->prepare('INSERT INTO tracks(id, name, album, added_by, playlist_id)
                                 VALUES(:id, :name, :album, :added_by, :playlist_id)
                                 ON DUPLICATE KEY UPDATE
                                 name= :name,
                                 album= :album,
                                 added_by = :added_by,
                                 playlist_id = :playlist_id');

foreach ($playlists as $list) {
    $playlistStatement->execute([
        'id' => $list->id,
        'name' => $list->name,
    ]);

    $songs = $api->getUserPlaylistTracks($list->owner->id, $list->id)->items;

    foreach ($songs as $song) {
        $trackStatement->execute([
            'id' => $song->track->id,
            'name' => $song->track->name,
            'album' => $song->track->album->name,
            'added_by' => $song->added_by->id,
            'playlist_id' => $list->id,
        ]);
    }
}

echo 'Lists updated';

// TODO: Get all daily playlists
// $playlistUrl = $baseUrl . 'users/arvid.b/playlists';

// TODO: Get song title

// TODO: Get songs for each playlist in turn, and check them for duplicates
