<?php

namespace Boivie\Daily\Controller;

use Boivie\Daily\Action\UpdateAction;
use Interop\Container\ContainerInterface;
use SpotifyWebAPI\SpotifyWebAPI;

class UpdateController extends BaseController
{
    public function update($request, $response, $args)
    {
        $api = new SpotifyWebAPI();

        $api->setAccessToken($this->getToken());

        $updateAction = new UpdateAction(
            $this->container->settings,
            [
                'api' => $api,
                'db' => $this->container->db
            ]
        );

        $status = $updateAction->getAllTracks();

        if ($status !== true) {
            $response->getBody()->write('Something went wrong');

            return $response;
        }

        $response->getBody()->write('Playlists updated');

        return $response;
    }
}
