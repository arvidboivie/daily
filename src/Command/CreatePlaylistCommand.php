<?php

namespace Boivie\Daily\Command;

use Boivie\Daily\Action\PlaylistAction;

class CreatePlaylistCommand extends BaseCommand
{
    public function run()
    {
        $playlistAction = new PlaylistAction($this->config);

        $playlistID = $playlistAction->createNewPlaylist();

        $status = $playlistAction->subscribeUserToPlaylist($playlistID);

        return $status;
    }
}
