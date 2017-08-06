<?php

class Model
{

    public function __construct()
    {
        //include $_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/libs/TwitterAPIExchange.php';
        require_once($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/libs/TwitterAPIExchange.php');
    }

////////////////////////////    Weather    ////////////////////////////

    public function get10DaysForecast()
    {
        $prop_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/config.ini.php');

        $json_string = file_get_contents("http://api.wunderground.com/api/" . $prop_array['weather_key']
            . "/forecast10day/q/Canada/Montreal.json");
        $parsed_json = json_decode($json_string, true);

        return $parsed_json;
    }

    public function getHourlyForecast()
    {
        $prop_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/config.ini.php');

        $json_string = file_get_contents("http://api.wunderground.com/api/" . $prop_array['weather_key']
            . "/hourly/q/Canada/Montreal.json");
        $parsed_json = json_decode($json_string, true);

        return $parsed_json;
    }

////////////////////////////    Twitter    ////////////////////////////

    public function getTweets($getQueryString)
    {
        $prop_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/config.ini.php');

        /** Set access tokens here - see: https://dev.twitter.com/apps/ **/
        $settings = array(
            'oauth_access_token' => $prop_array['oauth_access_token'],
            'oauth_access_token_secret' => $prop_array['oauth_access_token_secret'],
            'consumer_key' => $prop_array['consumer_key'],
            'consumer_secret' => $prop_array['consumer_secret']
        );

        /** URL for REST request, see: https://dev.twitter.com/docs/api/1.1/ **/
        $url = $prop_array['twitter_api_url'];
        $requestMethod = 'GET';

        $getField = '?' . $getQueryString;

        // using TwitterAPIExchange.php library to auth and get tweets
        $twitter = new TwitterAPIExchange($settings);

        return $twitter->setGetfield($getField)
            ->buildOauth($url, $requestMethod)
            ->performRequest();
    }

//////////////////////////    Photo Frame    //////////////////////////

    public function getPhotoFromFolder()
    {
        $imagesDir = $_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/public/usb/';

        $images = glob($imagesDir . '*.{jpg,JPG,jpeg,JPEG,png,PNG}', GLOB_BRACE);

        $randomImage = $images[array_rand($images)];

        //returning only file name
        return basename($randomImage);
    }

//////////////////////////    Photo Frame    //////////////////////////

    public function getGoogleCalendarEvents()
    {

        set_include_path(get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'] .
            '/home-dashboard/app/libs/Google/google-api-php-client-master/src');

        require_once 'Google/autoload.php';
        require_once 'Google/Client.php';



        $key_file = $_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/libs/Google/dash-cal';

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $key_file);

        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();

        $calendar_Service = new Google_Service_Calendar($client);


        $events = $calendar_Service->calendarList->listCalendarList();

        return $events;


    }


}