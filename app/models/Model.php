<?php

class Model
{
    public function get10DaysForecast()
    {
        $prop_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/config.ini.php');

        $json_string = file_get_contents("http://api.wunderground.com/api/". $prop_array['key']
            ."/forecast10day/q/Canada/Montreal.json");
        $parsed_json = json_decode($json_string, true);

        return $parsed_json;
    }

    public function getHourlyForecast()
    {
        $prop_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/config.ini.php');

        $json_string = file_get_contents("http://api.wunderground.com/api/". $prop_array['key']
            ."/hourly/q/Canada/Montreal.json");
        $parsed_json = json_decode($json_string, true);

        return $parsed_json;
    }
}