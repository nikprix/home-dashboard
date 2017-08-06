<?php

class Controller
{
    public function model($model)
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/home-dashboard/app/models/' . $model . '.php';
        return new $model();
    }

    public function view($view)
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/home-dashboard/app/views/' . $view . '.php';
    }
}