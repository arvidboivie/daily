<?php

namespace DailyDouble\Controller;

use Interop\Container\ContainerInterface;
use GuzzleHttp;

class BaseController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function getToken()
    {
        $spotify = $this->container->settings['spotify'];
        $vault_url = $this->container->settings['vault_url'];

        $client = new GuzzleHttp\Client();

        $request = $client->request(
            'GET',
            $vault_url.$spotify['client_id'].'/'.$spotify['playlist_user']
        );

        $response = json_decode($request->getBody(), true);

        if (empty($response['error']) === false) {
            $this->container->logger->error($response['error']);
            throw new Exception("Error getting token");
        }

        return $response['token'];
    }
}
