<?php

namespace EasyScf;

class Scf
{
    public $event;
    public $context;
    public function __construct($event, $context)
    {
        $this->event = $event;
        $this->context = $context;
    }

    public function run()
    {
        echo "Hello, World!";
    }
}
