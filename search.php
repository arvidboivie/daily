<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

// Store access and refresh token
$host = '***REMOVED***';
$db = '***REMOVED***';
$user = '***REMOVED***';
$password = '***REMOVED***';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$pdo = new \PDO($dsn, $user, $password);

$songStatement = $pdo->prepare('SELECT
                                    tracks.id AS track_id,
                                    tracks.name AS track_name,
                                    album,
                                    added_by,
                                    playlists.name AS playlist_name
                                FROM tracks
                                LEFT JOIN playlists ON playlists.id = tracks.playlist_id');

$songStatement->execute();

$songs = $songStatement->fetchAll();

$searchTerm = empty($_GET['search']) === false ? $_GET['search'] : 'love';

echo 'Search term: '.$searchTerm.'<br><br>';

$results = array_filter($songs, function($songObject) use ($searchTerm) {
    if (preg_match('/'.$searchTerm.'/i', $songObject['track_name']) === 1) {
        return true;
    }
    if (preg_match('/'.$searchTerm.'/i', $songObject['album']) === 1) {
        return true;
    }
});

foreach ($results as $result) {
    echo '"'.$result['track_name'].' - '.$result['album'].'" in '.$result['playlist_name'].'<br>';
}

// TODO: Get all daily playlists
// $playlistUrl = $baseUrl . 'users/arvid.b/playlists';

// TODO: Get song title

// TODO: Get songs for each playlist in turn, and check them for duplicates
