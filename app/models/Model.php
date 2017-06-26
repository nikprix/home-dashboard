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

        $json_string = file_get_contents("http://api.wunderground.com/api/". $prop_array['weather_key']
            ."/forecast10day/q/Canada/Montreal.json");
        $parsed_json = json_decode($json_string, true);

        return $parsed_json;
    }

    public function getHourlyForecast()
    {
        $prop_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/config.ini.php');

        $json_string = file_get_contents("http://api.wunderground.com/api/". $prop_array['weather_key']
            ."/hourly/q/Canada/Montreal.json");
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
            ->performRequest();;
    }



}