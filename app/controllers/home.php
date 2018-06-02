<?php
// Setting default timezone
date_default_timezone_set('America/New_York');

require_once $_SERVER['DOCUMENT_ROOT'] . '/home-dashboard/app/core/Controller.php';

class Home extends Controller
{
    public function index()
    {
        $this->view('home/index');

    }

    public function htmlspecialchars_array(array $array)
    {
        foreach ($array as $key => $val) {
            $array[$key] = (is_array($val)) ? htmlspecialchars_array($val) : htmlspecialchars($val);
        }
        return $array;
    }

    /////////////////////////////////////////////////
    //  Weather
    /////////////////////////////////////////////////

    public function meteo()
    {
        $model = $this->model('Model');

        $htmlDailyWeather = '';
        $htmlHourlyWeather = '';

        //////////////////////// Retrieving 8 days forecast ////////////////////////

        $dayArr = $model->getDailyForecast();

        $daysForecast = $dayArr['daily']['data'];

        // looping through the array of days (getting only first 8 days)
        for ($i = 0; $i < sizeof($daysForecast); $i++) {

            $weekDay = date('l', $daysForecast[$i]['time']);
            $monthName = date('F', $daysForecast[$i]['time']);
            $date = date('j', $daysForecast[$i]['time']);
            $weatherConditions = $daysForecast[$i]['summary'];
            $maxDegrees = round($daysForecast[$i]['temperatureHigh'],0,PHP_ROUND_HALF_UP);
            $minDegrees = round($daysForecast[$i]['temperatureLow'],0,PHP_ROUND_HALF_UP);
            $iconId = $daysForecast[$i]['icon'];


            // 'snow' heigth needs implementation depending from the project
            $snowAllDay = '';
            $snowDay = '';
            $snowNight = '';

            // replacing current day string with 'TODAY'. Also, here we are using default timezone
            ($weekDay == date('l') && $date == getdate(date("U"))['mday']) ? $weekDay = 'today' : $weekDay;

            $htmlDailyWeather .=
                '<div class="dayForecast">
                    <div class="weekDay">' . $weekDay . '
                    <span class="monthDay">' . $monthName . ' ' . $date . '</span>
                    </div>
                    <p class="conditions">' . $weatherConditions . '</p>

                    <p><canvas class="' . $iconId . '" width="40" height="40"></canvas></p>

                    <div class="temperature">
                    <span class="maxTemp">' . $maxDegrees . '&deg;</span>
                    <span class="lowTemp">' . $minDegrees . '&deg;</span>
                    </div>
                </div>';

        }

        $htmlDailyWeather =
            '<div id="dailyBlock">
                ' . $htmlDailyWeather . '
            </div>';


        //////////////////////// Retrieving hourly forecast ////////////////////////

        $hourlyArr = $model->getHourlyForecast();

        $hoursForecast = $hourlyArr['hourly']['data'];

        // looping through the array of hours (getting only first 11 hours)
        for ($i = 0; $i < 11; $i++) {

            date('l', $hoursForecast[$i]['time']);

            $hour = date('G', $hoursForecast[$i]['time']);
            $minute = date('i', $hoursForecast[$i]['time']);
            $hourlyCondition = $hoursForecast[$i]['summary'];
            $hourlyTemp = round($hoursForecast[$i]['temperature'],0,PHP_ROUND_HALF_UP);
            $hourlyIcon = $hoursForecast[$i]['icon'];

            $htmlHourlyWeather .=
                '<div class="hourlyForecast">
                    <div class="timeDisplay">
                        <span class="hoursMinutes">' . $hour . ':' . $minute . '</span>

                    </div>
                    <p class="hourlyConditions">' . $hourlyCondition . '</p>

                    <p><canvas class="' . $hourlyIcon . '" width="35" height="35"></canvas></p>

                    <div class="hourlyTemperature">
                        <span class="currentTemp">' . $hourlyTemp . '</span>
                        <span class="celsDegree">&deg;</span>
                    </div>
                </div>';
        }

        $htmlHourlyWeather =
            '<div id="hourlyBlock">
                <canvas id="hourlyWeatherChart" width="1300" height="120"></canvas>
                ' . $htmlHourlyWeather . '
            </div>';

        $htmlDailyWeather .= $htmlHourlyWeather;

        return $htmlDailyWeather;
    }

    /////////////////////////////////////////////////
    //  Twitter
    /////////////////////////////////////////////////

    public function twitter($getQueryString)
    {
        $model = $this->model('Model');

        // retrieving tweets JSON (as is, filtering and parsing is done on the client level)
        $tweets = $model->getTweets($getQueryString);

        return $tweets;
    }

    /////////////////////////////////////////////////
    //  Photo frame
    /////////////////////////////////////////////////

    public function photoFrame()
    {

        $model = $this->model('Model');

        $photoPath = $model->getPhotoFromFolder();

        $imagePath = '../../public/usb/' . $photoPath;

        // retrieving image's metadata to assign correct class for displaying
        list($width, $height) = getimagesize($imagePath);
        // getting correct class
        $class = ($width > $height) ? 'landscape' : 'portrait';


        $htmlPhotoFrame =
            '<section class="polaroids">
                <img id="imagePin" src="/home-dashboard/public/pic/pin-png-39474.png" >
                <img id="familyPhoto" src="/home-dashboard/public/usb/' . $photoPath . '" class="'. $class . '">
            </section>';

        return $htmlPhotoFrame;
    }


    /////////////////////////////////////////////////
    //  Calendar
    /////////////////////////////////////////////////

    public function calendar()
    {
        $model = $this->model('Model');

        $allEvents = $model->getGoogleCalendarEvents();

        $eventsHtml = json_encode( (array)$allEvents, JSON_UNESCAPED_UNICODE);

        return $eventsHtml;

    }

}