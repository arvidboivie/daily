<?php

namespace Boivie\DailyDouble;

use SpotifyWebAPI\SpotifyWebAPI;

class Search
{

    protected $api;

    public function __construct(SpotifyWebAPI $api)
    {
        $this->api = $api;
    }

    public function search($searchTerm)
    {
        $host = '***REMOVED***';
        $db = '***REMOVED***';
        $user = '***REMOVED***';
        $password = '***REMOVED***';
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $pdo = new \PDO($dsn, $user, $password);

        $songStatement = $pdo->prepare(
            'SELECT
            tracks.id AS track_id,
            tracks.name AS track_name,
            album,
            added_by,
            playlists.name AS playlist_name
            FROM tracks
            LEFT JOIN playlists ON playlists.id = tracks.playlist_id
            ORDER BY playlist_name'
        );

        $songStatement->execute();

        $songs = $songStatement->fetchAll();

        $results = array_filter($songs, function ($songObject) use ($searchTerm) {
            if (preg_match('/'.$searchTerm.'/i', $songObject['track_name']) === 1) {
                return true;
            }
            if (preg_match('/'.$searchTerm.'/i', $songObject['album']) === 1) {
                return true;
            }
        });

        return $results;
    }

    public function getSongs($searchTerm)
    {
        $host = '***REMOVED***';
        $db = '***REMOVED***';
        $user = '***REMOVED***';
        $password = '***REMOVED***';
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $pdo = new \PDO($dsn, $user, $password);

        $songStatement = $pdo->prepare(
            'SELECT
            tracks.name AS label,
            tracks.playlist_id AS playlist,
            playlists.creator AS creator
            FROM tracks
            LEFT JOIN playlists ON playlists.id = tracks.playlist_id
            WHERE tracks.name LIKE :search'
        );

        $songStatement->execute([
            'search' => '%'.$searchTerm.'%',
        ]);

        $songs = $songStatement->fetchAll(\PDO::FETCH_ASSOC);

        return $songs;
    }
}
