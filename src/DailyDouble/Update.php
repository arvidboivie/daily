<?php

namespace Boivie\DailyDouble;

use SpotifyWebAPI\SpotifyWebAPI;

class Update
{
    protected $api;

    public function __construct(SpotifyWebAPI $api)
    {
        $this->api = $api;
    }

    public function updatePlaylists()
    {
        $playlists = $this->api->getUserPlaylists('arvid.b', ['limit' => 50]);

        $playlists = array_filter($playlists->items, function ($list) {
            if (preg_match('/Dagens Låt \d{2}/', $list->name) === 1) {
                return true;
            }
        });

        $host = '***REMOVED***';
        $db = '***REMOVED***';
        $user = '***REMOVED***';
        $password = '***REMOVED***';
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $pdo = new \PDO($dsn, $user, $password);

        $playlistStatement = $pdo->prepare(
            'INSERT INTO playlists(id, name, creator)
            VALUES(:id, :name, :creator)
            ON DUPLICATE KEY UPDATE
            name= :name,
            creator= :creator'
        );

        $trackStatement = $pdo->prepare(
            'INSERT INTO tracks(id, name, album, added_by, playlist_id)
            VALUES(:id, :name, :album, :added_by, :playlist_id)
            ON DUPLICATE KEY UPDATE
            name= :name,
            album= :album,
            added_by = :added_by,
            playlist_id = :playlist_id'
        );

        foreach ($playlists as $list) {
            $playlistStatement->execute([
                'id' => $list->id,
                'name' => $list->name,
                'creator' => $list->owner->id,
            ]);

            $songs = $this->api->getUserPlaylistTracks($list->owner->id, $list->id)->items;

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

        return true;
    }
}
