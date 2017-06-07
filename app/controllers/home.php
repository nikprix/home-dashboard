<?php

require_once  $_SERVER['DOCUMENT_ROOT'].'/home-dashboard/app/core/Controller.php';

class Home extends Controller
{
    public function index()
    {
        $this->view('home/index');

//        $model = $this->model('Model');
//        $model->name = 'TEST';
//        echo $model->name;
    }

    public function htmlspecialchars_array(array $array)
    {
        foreach ($array as $key => $val) {
            $array[$key] = (is_array($val)) ? htmlspecialchars_array($val) : htmlspecialchars($val);
        }
        return $array;
    }

    /////////////////////////////////////////////////
    //  Meteo
    /////////////////////////////////////////////////

    public function meteo()
    {

//        $meteo = '<div id="cont_8e20b0bbfeddbe1a676b3f71fdfaf29b"><script type="text/javascript" async src="https://www.theweather.net/wid_loader/8e20b0bbfeddbe1a676b3f71fdfaf29b"></script></div>';

       $meteo = '<a href="https://www.accuweather.com/en/ca/montreal/h3a/current-weather/56186" class="aw-widget-legal"></a><div id="awtd1496808182210" class="aw-widget-36hour"  data-locationkey="56186" data-unit="c" data-language="en-us" data-useip="false" data-uid="awtd1496808182210" data-editlocation="false"></div><script type="text/javascript" src="https://oap.accuweather.com/launch.js"></script>';

        return $meteo;
    }

}