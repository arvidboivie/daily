<?php

namespace DailyDouble\Command;

use DailyDouble\Controller\Update;
use DailyDouble\Helper\SpotifyApiHelper;
use Noodlehaus\Config;
use \PDO;

class UpdateCommand
{

    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function run()
    {
        $db = $this->config->get('database');

        $dsn = "mysql:host=".$db['host'].";dbname=".$db['name'].";charset=".$db['charset'];

        $pdo = new PDO($dsn, $db['user'], $db['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;

        $spotify = $this->config->get('spotify');

        $api = (new SpotifyApiHelper(
            $db,
            $spotify['client_id'],
            $spotify['client_secret'],
            $spotify['redirect_URI']
        ))->getApiWrapper();

        $update = new Update($api, $db);

        $status = $update->updatePlaylists();

        if ($status !== true) {
            echo 'Something went wrong';
        }

        echo 'Playlists updated';
    }
}
