<?php

class App
{
    protected $controller = 'home';
    protected $method = 'index';


    public function __construct()
    {

        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        call_user_func([$this->controller, $this->method], $this);

    }


}
