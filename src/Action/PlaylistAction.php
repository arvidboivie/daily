<?php

namespace Boivie\Daily\Action;

use Carbon\Carbon;
use SpotifyWebAPI\SpotifyWebAPI;

class PlaylistAction extends BaseAction
{
    public function __construct($config, $addons = null)
    {
        parent::__construct($config);

        $this->addAPI($addons['api']);
    }

    public function createNewPlaylist()
    {
        $spotifyConfig = $this->config->get('spotify');

        $latest = $this->getLatestPlaylist();

        setlocale(LC_TIME, 'sv-SE');
        $now = Carbon::now();

        $playlistName = sprintf(
            $spotifyConfig['new_playlist_pattern'],
            ++$latest['number'],
            $now->formatLocalized('%B'),
            $now->year
        );

        $newList = $this->api->createUserPlaylist(
            $spotifyConfig['playlist_user'],
            [
                'name' => $playlistName,
                'public' => false,
                'collaborative' => true,
            ]
        );

        return $newList->id;
    }

    public function subscribeUserToPlaylist($playlistID)
    {
        $spotifyConfig = $this->config->get('spotify');

        $api = new SpotifyWebAPI();

        $api->setAccessToken(
            $this->getAPIToken($spotifyConfig['collaborative_user'])
        );

        $api->followPlaylist(
            $spotifyConfig['playlist_user'],
            $playlistID,
            [
                'public' => false,
            ]
        );

        return true;
    }
}
