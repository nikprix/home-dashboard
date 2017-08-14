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

        $dayArr = $model->get10DaysForecast();

        $daysForecast = $dayArr['forecast']['simpleforecast']['forecastday'];

        // looping through the array of days (getting only first 8 days)
        for ($i = 0; $i < sizeof($daysForecast) - 2; $i++) {

            $weekDay = $daysForecast[$i]['date']['weekday'];
            $monthName = $daysForecast[$i]['date']['monthname'];
            $date = $daysForecast[$i]['date']['day'];
            $weatherConditions = $daysForecast[$i]['conditions'];
            $maxDegrees = $daysForecast[$i]['high']['celsius'];
            $minDegrees = $daysForecast[$i]['low']['celsius'];
            $iconUrl = $daysForecast[$i]['icon_url'];
            // 'snow' heigth needs implementation depending from the project
            $snowAllDay = '';
            $snowDay = '';
            $snowNight = '';

            // changing icon (currently not replacing)
            $newIconType = 'k';
            $replace = 'k';
            $newIconUrl = str_replace('/' . $replace . '/', '/' . $newIconType . '/', $iconUrl);

            // replacing current day string with 'TODAY'. Also, here we are using default timezone
            ($weekDay == date('l') && $date == getdate(date("U"))['mday']) ? $weekDay = 'today' : $weekDay;

            $htmlDailyWeather .=
                '<div class="dayForecast">
                    <div class="weekDay">' . $weekDay . '
                    <span class="monthDay">' . $monthName . ' ' . $date . '</span>
                    </div>
                    <p class="conditions">' . $weatherConditions . '</p>
                    <p><img src="' . $newIconUrl . '"></p>
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

        $hoursForecast = $hourlyArr['hourly_forecast'];

        // looping through the array of days (getting only first 8 days)
        for ($i = 0; $i < sizeof($hoursForecast); $i++) {

            $hour = $hoursForecast[$i]['FCTTIME']['hour'];
            $minute = $hoursForecast[$i]['FCTTIME']['min'];
            $hourlyTemp = $hoursForecast[$i]['temp']['metric'];
            $feelsLikeTemp = $hoursForecast[$i]['feelslike']['metric'];
            $hourlyIcon = $hoursForecast[$i]['icon_url'];
            $hourlyCondition = $hoursForecast[$i]['condition'];

            $htmlHourlyWeather .=
                '<div class="hourlyForecast">
                    <div class="timeDisplay">
                        <span class="hoursMinutes">' . $hour . ':' . $minute . '</span>

                    </div>
                    <p class="hourlyConditions">' . $hourlyCondition . '</p>
                    <p><img class="hourlyWeatherIcon" src="' . $hourlyIcon . '"></p>
                    <div class="hourlyTemperature">
                        <span class="currentTemp">' . $hourlyTemp . '</span>
                        <span class="celsDegree">&deg;</span>
                    </div>
                </div>';

            // breaking the loop when 10 hours weather is retrieved
            if ($i == 10) break;
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