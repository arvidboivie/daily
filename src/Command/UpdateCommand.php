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
        $dbConfig = $this->config->get('database');

        $dsn = "mysql:host=".$dbConfig['host'].";dbname=".$dbConfig['name'].";charset=".$dbConfig['charset'];

        $db = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $spotify = $this->config->get('spotify');

        $api = (new SpotifyApiHelper(
            $db,
            $spotify['client_id'],
            $spotify['client_secret'],
            $spotify['redirect_URI']
        ))->getApiWrapper();

        $update = new Update($api, $db);

        $status = $update->updatePlaylists();

        return $status;
    }
}
