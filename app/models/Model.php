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

//////////////////////////    Calendar    //////////////////////////

    public function getGoogleCalendarEvents()
    {
        $prop_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/config.ini.php');

        // ! important - google-api-php-lib was loaded using composer
        require_once $_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/libs/google-apis/autoload.php';

        // getting google auth file's path
        $key_file = $_SERVER['DOCUMENT_ROOT'] . $prop_array['gogl_setting_file'];

        // loading auth credentials
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $key_file);

        $client = new Google_Client();
        // authenticating
        $client->useApplicationDefaultCredentials();
        // ! important - setting scopes
        $client->setScopes(['https://www.googleapis.com/auth/calendar.readonly']);


        $calendar_Service = new Google_Service_Calendar($client);


        $calendarList = $calendar_Service->calendarList->listCalendarList();

        $eventsArray = array();


        while(true) {
            foreach ($calendarList->getItems() as $calendarListEntry) {

                echo $calendarListEntry->getSummary()."\n";


                // get events
                //$events = $calendar_Service->events->listEvents($calendarListEntry->id);
                $events = $calendar_Service->events->listEvents($calendarListEntry->id, array
                ('timeMin'=>'2017-08-06T00:00:00-04:00', 'timeMax'=>'2017-08-09T23:59:59-04:00'));


                foreach ($events->getItems() as $event) {
                    echo "<br>-----".$event->getSummary(). "-------<br>";

                    array_push($eventsArray, $event->getSummary());

                }
            }

            $pageToken = $calendarList->getNextPageToken();

            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $calendarList = $calendar_Service->calendarList->listCalendarList($optParams);
            } else {
                break;
            }
        }

        return $events;


    }


}