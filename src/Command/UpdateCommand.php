<?php

namespace Boivie\Daily\Command;

use Boivie\Daily\Action\UpdateAction;

class UpdateCommand extends BaseCommand
{
    public function run()
    {
        $update = new UpdateAction($this->config);

        $status = $update->getTracksFromCurrentPlaylist();

        return $status;
    }
}
