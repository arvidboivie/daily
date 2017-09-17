<?php

namespace Boivie\Daily\Action;

use \PDO;
use GuzzleHttp;
use SpotifyWebAPI\SpotifyWebAPI;

class BaseAction
{
    protected $config;

    protected $api;

    protected $db;

    public function __construct($config)
    {
        $this->config = $config;
    }

    protected function getLatestPlaylist()
    {
        $this->addAPI();

        $spotifyConfig = $this->config->get('spotify');

        $playlists = $this->api->getUserPlaylists(
            $spotifyConfig['user'],
            ['limit' => 50]
        );

        $pattern = $spotifyConfig['playlist_pattern'];

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

        return $latest;
    }

    protected function addAPI($api = null)
    {
        if ($this->api === null) {
            if ($api !== null) {
                $this->api = $api;
            }

            $vault_url = $this->config->get('vault_url');
            $spotify = $this->config->get('spotify');

            $client = new GuzzleHttp\Client();

            $request = $client->request(
                'GET',
                $vault_url.$spotify['client_id'].'/'.$spotify['playlist_user']
            );

            $response = json_decode($request->getBody(), true);

            if (empty($response['error']) === false) {
                fwrite(STDOUT, $response['error']);
                return false;
            }

            $this->api = new SpotifyWebAPI();

            $this->api->setAccessToken($response['token']);
        }
    }

    protected function addDB($db = null)
    {
        if ($this->db === null) {
            if ($db !== null) {
                $this->db = $db;
            }

            $dbConfig = $this->config->get('database');
            $dsn = "mysql:host=".$dbConfig['host'].";dbname=".$dbConfig['name'].";charset=".$dbConfig['charset'];
            $this->db = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
    }
}
