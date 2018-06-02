<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/models/entities/Event.php';

class Model
{


    public function __construct()
    {
        //include $_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/libs/TwitterAPIExchange.php';
        require_once($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/libs/TwitterAPIExchange.php');
    }

////////////////////////////    Weather    ////////////////////////////

    public function getDailyForecast()
    {
        $prop_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/config.ini.php');

        $json_string = file_get_contents("https://api.darksky.net/forecast/" . $prop_array['weather_key']
            . "/45.4548,-73.5699?exclude=currently,minutely,hourly,flags&units=ca");
        $parsed_json = json_decode($json_string, true);

        return $parsed_json;
    }

    public function getHourlyForecast()
    {
        $prop_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/config.ini.php');

        $json_string = file_get_contents("https://api.darksky.net/forecast/" . $prop_array['weather_key']
            . "/45.4548,-73.5699?exclude=currently,minutely,daily,flags&units=ca");
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
        // ! important - google-api-php-lib was loaded using composer
        require_once $_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/libs/google-apis/autoload.php';

        $prop_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/config.ini.php');

        // getting google auth file's path
        $key_file = $_SERVER['DOCUMENT_ROOT'] . $prop_array['gogl_setting_file'];

        // loading auth credentials
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $key_file);

        $client = new Google_Client();
        // authenticating
        $client->useApplicationDefaultCredentials();
        // ! important - setting scopes
        $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);


        $calendar_Service = new Google_Service_Calendar($client);


        $calendarList = $calendar_Service->calendarList->listCalendarList();

        $eventsArray = array();

        // events params
        $optParams = array(
            'orderBy' => 'startTime',
            'singleEvents' => TRUE,
            'timeMin' => date('c', time() - 60 * 60 * 24), // current time minus 24 hours (1 day)
            'timeMax' => date('c', time() + 60 * 60 * 24 * 3) // current time plus  3 days
        );


        while (true) {
            foreach ($calendarList->getItems() as $calendarListEntry) {

             //   echo $calendarListEntry->getSummary() . "\n";


                // get events
                //$events = $calendar_Service->events->listEvents($calendarListEntry->id);
                $events = $calendar_Service->events->listEvents($calendarListEntry->id, $optParams);


                foreach ($events->getItems() as $event) {

                    $eventTitle = $event->getSummary();

                    $eventObj = new Event($eventTitle, $event->getStart()->getDateTime(), $event->getEnd()->getDateTime());

                    // DEBUGGING
//                    echo "<br>-----" . $event->getSummary() . "-------<br>";
//                    echo "-----" . $event->getStart()->getDateTime() . "-------<br>";
//                    echo "-----" . $event->getEnd()->getDateTime() . "-------<br>";
//                    echo "<br>";

                    array_push($eventsArray, $eventObj);

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

        return $eventsArray;

    }


}