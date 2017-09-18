#!/usr/bin/env php
<?php

require_once '../vendor/autoload.php';

use Boivie\Daily\Command\CreatePlaylistCommand;
use Boivie\Daily\Command\UpdateCommand;
use Noodlehaus\Config;

$jobby = new \Jobby\Jobby();
$config = Config::load('../config.yml');
$jobbyConfig = $config->get('jobby');

$jobby->add('daily/updateLatest', array(
    'closure' => function () use ($config) {
        return (new UpdateCommand($config))->run();
    },
    'schedule' => $jobbyConfig['schedule']['update'],
    'output' => $jobbyConfig['log'],
    'debug' => $jobbyConfig['debug'],
    'enabled' => true,
));

$jobby->add('daily/createPlaylist', array(
    'closure' => function () use ($config) {
        return (new CreatePlaylistCommand($config))->run();
    },
    'schedule' => $jobbyConfig['schedule']['new_playlist'],
    'output' => $jobbyConfig['log'],
    'debug' => $jobbyConfig['debug'],
    'enabled' => true,
));

$jobby->run();
