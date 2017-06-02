<?php

class Home extends Controller
{
    public function index()
    {
        $this->view('home/index');

//        $model = $this->model('Model');
//        $model->name = 'TEST';
//        echo $model->name;
    }

}