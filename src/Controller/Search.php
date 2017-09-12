<?php

namespace DailyDouble\Controller;

class Search
{
    /**
     * @var \PDO
     */
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    //TODO: Remove or use this function
    public function search($searchTerm)
    {
        $songStatement = $this->db->prepare(
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
        $songStatement = $this->db->prepare(
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
