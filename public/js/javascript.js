$(function() {
    clock();
});

/* clock */

var clock_timeout;

function clock()
{
    days  = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    months  = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];

    now          = new Date;
    hour        = now.getHours();
    min          = now.getMinutes();
    sec          = now.getSeconds();
    week_day = days[now.getDay()];
    day         = now.getDate();
    month         = months[now.getMonth()];
    year        = now.getFullYear();

    if (sec < 10){sec0 = "0";}else{sec0 = "";}
    if (min < 10){min0 = "0";}else{min0 = "";}
    if (hour < 10){hour0 = "0";}else{hour0 = "";}

    clock_hour   = hour + ":" + min0 + min;
    clock_date    = "<span class='clock_grey'>" + week_day + "</span> " + day + " " + month + " <span" +
        " class='clock_grey'>" + year + "</span>";
    clock_content = "<div class='clock_hour'>" + clock_hour + "</div><div class='clock_date'>" + clock_date + "</div>";

    $("#clock").html(clock_content);

    clock_timeout = setTimeout("clock()", 1000);
}
