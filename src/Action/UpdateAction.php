<?php

namespace Boivie\Daily\Action;

use SpotifyWebAPI\SpotifyWebAPI;

class UpdateAction
{
    /**
     * @var SpotifyWebAPI
     */
    private $api;

    /**
     * @var PDO
     */
    private $db;

    public function __construct(SpotifyWebAPI $api, \PDO $db)
    {
        $this->api = $api;
        $this->db = $db;
    }

    public function getTracksFromCurrentPlaylist($user, $pattern)
    {
        $playlists = $this->api->getUserPlaylists($user, ['limit' => 50]);

        $latest = array_reduce($playlists->items, function ($carry, $list) use ($pattern) {
            $matches = [];

            if (preg_match($pattern, $list->name, $matches) === 1) {
                if ((int)$matches[1] > $carry['number']) {
                    return [
                        'number' => (int)$matches[1],
                        'list' => $list,
                    ];
                }

                return $carry;
            }

            return $carry;
        }, [
            'number' => 0,
            'list' => null,
        ]);

        $trackStatement = $this->db->prepare(
            'INSERT INTO tracks(id, name, album, added_by, playlist_id)
            VALUES(:id, :name, :album, :added_by, :playlist_id)
            ON DUPLICATE KEY UPDATE
            name= :name,
            album= :album,
            added_by = :added_by,
            playlist_id = :playlist_id'
        );

        $tracks = $this->api->getUserPlaylistTracks(
            $latest['list']->owner->id,
            $latest['list']->id
        )->items;

        foreach ($tracks as $track) {
            $trackStatement->execute([
            'id' => $track->track->id,
            'name' => $track->track->name,
            'album' => $track->track->album->name,
            'added_by' => $track->added_by->id,
            'playlist_id' => $latest['list']->id,
            ]);
        }

        return true;
    }

    public function getAllTracks($user, $pattern)
    {
        $playlists = $this->api->getUserPlaylists($user, ['limit' => 50]);

        $playlists = array_filter($playlists->items, function ($list) use ($pattern) {
            if (preg_match($pattern, $list->name) === 1) {
                return true;
            }
        });

        $playlistStatement = $this->db->prepare(
            'INSERT INTO playlists(id, name, creator)
            VALUES(:id, :name, :creator)
            ON DUPLICATE KEY UPDATE
            name= :name,
            creator= :creator'
        );

        $trackStatement = $this->db->prepare(
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
