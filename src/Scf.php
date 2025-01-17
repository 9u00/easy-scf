<?php

namespace EasyScf;

class Scf
{
    public $event;
    public $context;
    public $controller;
    public $model;
    public $validate;
    public $routes;
    public $params;
    public $body;
    public function __construct($event, $context)
    {
        $this->event = $event;
        $this->context = $context;
        $this->routes = require 'routes.php';
    }

    public function run()
    {
        $route = new Route($this->event, $this->context->function_name);
        list($isMatch, $controller, $action, $params, $body) = $route->init();
        if (!$isMatch) {
            var_dump('404');
            return '404';
        }
        var_dump([
            'controller' => $controller,
            'action' => $action,
            'params' => $params,
            'body' => $body,
        ]);
        return [
            'controller' => $controller,
            'action' => $action,
            'params' => $params,
            'body' => $body,
        ];
    }
}
