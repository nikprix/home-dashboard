<?php

class App
{
    protected $controller = 'home';
    protected $method = 'index';


    public function __construct()
    {

        require_once $_SERVER['DOCUMENT_ROOT'].'/home_dashboard/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        call_user_func([$this->controller, $this->method], $this);

    }


}
