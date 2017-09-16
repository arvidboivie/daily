<?php

namespace DailyDouble\Controller;

use DailyDouble\Action\UpdateAction;
use Interop\Container\ContainerInterface;
use SpotifyWebAPI\SpotifyWebAPI;

class UpdateController extends BaseController
{
    public function update($request, $response, $args)
    {
        $api = new SpotifyWebAPI();

        $api->setAccessToken($this->getToken());

        $update = new UpdateAction($api, $this->container->db);

        $spotify = $this->container->settings['spotify'];

        $status = $update->updatePlaylists(
            $spotify['playlist_user'],
            $spotify['playlist_pattern']
        );

        if ($status !== true) {
            $response->getBody()->write('Something went wrong');

            return $response;
        }

        $response->getBody()->write('Playlists updated');

        return $response;
    }

    public function updateLatest($request, $response, $args)
    {
        $api = new SpotifyWebAPI();

        $api->setAccessToken($this->getToken());

        $update = new UpdateAction($api, $this->container->db);

        $spotify = $this->container->settings['spotify'];

        $status = $update->updateLatestPlaylist(
            $spotify['playlist_user'],
            $spotify['playlist_pattern']
        );

        if ($status !== true) {
            $response->getBody()->write('Something went wrong');

            return $response;
        }

        $response->getBody()->write('Playlists updated');

        return $response;
    }
}
