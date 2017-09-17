<?php

namespace Boivie\Daily\Command;

class BaseCommand
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }
}
