<?php

namespace Boivie\Daily\Command;

use \PDO;
use Boivie\Daily\Controller\Update;
use GuzzleHttp;
use Noodlehaus\Config;
use SpotifyWebAPI\SpotifyWebAPI;

class UpdateCommand
{

    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function run()
    {
        $vault_url = $this->config->get('vault_url');
        $spotify = $this->config->get('spotify');

        $client = new GuzzleHttp\Client();

        $request = $client->request(
            'GET',
            $vault_url.$spotify['client_id'].'/'.$spotify['playlist_user']
        );

        $response = json_decode($request->getBody());

        if (empty($response['error']) === false) {
            fwrite(STDOUT, $response['error']);
            return false;
        }

        $api = new SpotifyWebAPI();

        $api->setAccessToken($response['token']);

        $update = new Update($api, $this->getDB($config));

        $status = $update->getTracksFromCurrentPlaylist(
            $spotify['playlist_user'],
            $spotify['playlist_pattern']
        );

        return $status;
    }

    private function getDB($config)
    {
        $dbConfig = $this->config->get('database');
        $dsn = "mysql:host=".$dbConfig['host'].";dbname=".$dbConfig['name'].";charset=".$dbConfig['charset'];
        $db = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $db;
    }
}
