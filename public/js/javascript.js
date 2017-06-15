$(function () {
    clock();
    meteo();
    buildLineGraph();

    // leave it at the end
    fixWeatherConditionsLength();
});

/* clock */

var clock_timeout;

function clock() {
    days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];

    now = new Date;
    hour = now.getHours();
    min = now.getMinutes();
    sec = now.getSeconds();
    week_day = days[now.getDay()];
    day = now.getDate();
    month = months[now.getMonth()];
    year = now.getFullYear();

    if (sec < 10) {
        sec0 = "0";
    } else {
        sec0 = "";
    }
    if (min < 10) {
        min0 = "0";
    } else {
        min0 = "";
    }
    if (hour < 10) {
        hour0 = "0";
    } else {
        hour0 = "";
    }

    // displaying 0 for hours, minutes that less than 10, conditions are above
    clock_hour = hour + ":" + min0 + min;
    // displaying the date
    clock_date = "<span class='clock_grey'>" + week_day + "</span> " + day + " " + month + " <span" +
        " class='clock_grey'>" + year + "</span>";
    // displaying the time + date
    clock_content = "<div class='clock_hour'>" + clock_hour + "</div><div class='clock_date'>" + clock_date + "</div>";

    $("#clock").html(clock_content);

    clock_timeout = setTimeout("clock()", 1000);
}


/* meteo */

var meteo_timeout;

function meteo ()
{
    $.ajax({
        async : false,
        type: "GET",
        url: "../app/core/ajax.php",
        data: "block=meteo",
        success: function(html){
            $("#meteo").html(html);
        }
    });

    meteo_timeout = setTimeout("meteo()", 3600000);
}

/* builds simple graph using chartjs.org lib */
function buildLineGraph()
{
    var ctx = document.getElementById("hourlyWeatherChart");

    // creating an empty array to store hours in it
    var allHours = [];
    // retrieving all times from the page:
    $("span.hoursMinutes").each(function() {
        // append values to an array
        allHours.push($(this).text());
    });

    // creating an empty array to store temperatures in it
    var allHourlyTemps = [];
    // retrieving all times from the page:
    $("span.currentTemp").each(function() {
        // append values to an array
        allHourlyTemps.push($(this).text());
    });

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: allHours,
            datasets: [{
                label: 'Hourly temperatures',
                data: allHourlyTemps,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
}


/* reduces font size*/
function fixWeatherConditionsLength(){
    // looping through each div with class 'conditions'
    $('p.conditions').each(function() {
        // if length of the string is more than 14 chars, it will get hidden, so need to apply fix below
        if($(this).text().length > 14)
        {
            // getting current div height
            var conditionsHeight = $(this).height();
            // calculating new height
            var newCondHeight = conditionsHeight * 2 + 3;
            $('p.conditions').height(newCondHeight);
            $('p.conditions').css({'white-space': 'pre-wrap'});
            // need to break the loop since it's enough to apply css only once
            return false;
        }
    });

    // looping through each div with class 'hourlyConditions'
    $('p.hourlyConditions').each(function() {
        // if length of the string is more than 13 chars, it will get hidden, so need to apply fix below
        if($(this).text().length > 13)
        {
            // getting current div height
            var hourlyConditionsHeight = $(this).height();
            // calculating new height
            var newHourlyCondHeight = hourlyConditionsHeight * 2 + 3;
            console.log(newHourlyCondHeight);
            $('p.hourlyConditions').height(newHourlyCondHeight);
            $('p.hourlyConditions').css({'white-space': 'pre-wrap'});
            // need to break the loop since it's enough to apply css only once
            return false;
        }
    });
}
