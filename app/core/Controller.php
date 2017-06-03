<?php

class Controller
{
    public function model($model)
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/home_dashboard/app/models/' . $model . '.php';
        return new $model();
    }

    public function view($view)
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/home_dashboard/app/views/' . $view . '.php';
    }
}