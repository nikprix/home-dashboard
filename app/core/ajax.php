<?php

header('Content-type: text/html; charset=utf-8');

require_once $_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/controllers/home.php';

$homeContr = new Home;

// proceeding only if client request asks to display specific block
if (isset($_REQUEST['block'])) {
    $block = $_REQUEST['block'];
} else if (isset($_POST['block'])){
    $block = $_POST['block'];
} else {
    $block = 'none';
}

/////////////////////////////////////////////////
//  METEO
/////////////////////////////////////////////////

if ($block == 'meteo') {
    echo $homeContr->meteo();
}

/////////////////////////////////////////////////
//  TWITTER
/////////////////////////////////////////////////

if ($block == 'twitter') {

    // retrieving search query
    $tweetsGetQueryString = $_POST['q'];

    echo $homeContr->twitter($tweetsGetQueryString);
}

/////////////////////////////////////////////////
//  PHOTO FRAME
/////////////////////////////////////////////////

if ($block == 'photoFrame') {
    echo $homeContr->photoFrame();
<<<<<<< HEAD
=======
}


/////////////////////////////////////////////////
//  CALENDAR
/////////////////////////////////////////////////

if ($block == 'calendar') {
    echo $homeContr->calendar();
>>>>>>> f718f9d01b5631caa7892e59722ecde5d7c6a69e
}