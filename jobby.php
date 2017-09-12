<?php

//
// Add this line to your crontab file:
//
// * * * * * cd /path/to/project && php jobby.php 1>> /dev/null 2>&1
//

require_once 'vendor/autoload.php';

use Boivie\DailyDouble\Command\UpdateCommand;
use Noodlehaus\Config;

$jobby = new \Jobby\Jobby();
$config = Config::load('config.yml');

$jobby->add('UpdateCommand', array(
    'command' => (new UpdateCommand($config))->run(),
    'schedule' => '*/15 * * * *',
    'output' => 'logs/command.log',
    'enabled' => true,
));

$jobby->run();
