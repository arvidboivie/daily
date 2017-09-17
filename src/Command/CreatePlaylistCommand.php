<?php

namespace Boivie\Daily\Command;

use Boivie\Daily\Action\PlaylistAction;

class CreatePlaylistCommand extends BaseCommand
{
    public function run()
    {
        $playlistAction = new PlaylistAction($this->config);

        $playlistID = $playlistAction->createNewPlaylist();

        $status = $playlistAction->subscribeUserToPlaylist(
            $this->config->get('spotify')['collaborative_user'],
            $playlistID
        );

        return $status;
    }
}
