<?php

header('Content-type: text/html; charset=utf-8');

require_once $_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/controllers/home.php';

$homeContr = new Home;

if (isset($_REQUEST['block'])) {
    $block = $_REQUEST['block'];
} else {
    $block = 'none';
}

/////////////////////////////////////////////////
//  METEO
/////////////////////////////////////////////////

if ($block == 'meteo') {
    echo $homeContr->meteo();
}
