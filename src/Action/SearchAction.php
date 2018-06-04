<?php

namespace Boivie\Daily\Action;

class SearchAction
{
    /**
     * @var \PDO
     */
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function getSongs($searchTerm)
    {
        $songStatement = $this->db->prepare(
            'SELECT
            tracks.name AS label,
            tracks.playlist_id AS playlist,
            playlists.creator AS creator
            playlists.name AS playlist_name
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
