$(function () {
    clock();
    meteo();
    getPicture();
    getCalendar();

    if (typeof TWEETS != 'undefined') {
        TWEETS.loadTweets();
    }
});

/** retrieves clock **/

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
    clock_date = "<span class='clock_grey' id='weekDay'>" + week_day + "</span> " + day + " " + month + " <span" +
        " class='clock_grey'>" + year + "</span>";
    // displaying the time + date
    clock_content = "<div class='clock_hour'>" + clock_hour + "</div><div class='clock_date'>" + clock_date + "</div>";

    $("#clock").html(clock_content);

    clock_timeout = setTimeout("clock()", 1000);
}

/** retrieves weather info **/

//var meteo_timeout;

function meteo() {
    $.ajax({
        async: false,
        type: "GET",
        url: "../app/core/ajax.php",
        data: "block=meteo",
        success: function (html) {
            $("#meteo").html(html);
            // fixing Weather conditions
            fixWeatherConditionsLength();
            // building graph using received hourly temperatures
            buildLineGraph();
        }
    });
    // setting icons
    setSkycons();
    console.log('weather refreshed!');
    // meteo_timeout = setTimeout("meteo()", 3600000);
}

/** Sets icons for weather **/

// https://github.com/maxdow/skycons
// If you want to add more colors :
// var skycons = new Skycons({"monochrome": false});
// you can now customize the color of different parts
// main, moon, fog, fogbank, cloud, snow, leaf, rain, sun
// var skycons = new Skycons({
//  "monochrome": false,
//  "colors" : {
//    "cloud" : "#F00"
//  }
//  });

function setSkycons() {
    var i,
        icons = new Skycons({
            "monochrome": false,
              "colors" : {
                  "cloud" : "#a2bee4",
                  "moon": "#ccc"
            },
            "resizeClear": true // nasty android hack
        }),
        list  = [ // listing of all possible icons
            "clear-day",
            "clear-night",
            "partly-cloudy-day",
            "partly-cloudy-night",
            "cloudy",
            "rain",
            "sleet",
            "snow",
            "wind",
            "fog"
        ];

    // loop thru icon list array
    for(i = list.length; i--;) {
        var weatherType = list[i], // select each icon from list array

        // icons will have the name in the array above attached to the
        // canvas element as a class so let's hook into them.
            elements = document.getElementsByClassName( weatherType );

        // loop thru the elements now and set them up
        for (e = elements.length; e--;) {
            icons.set(elements[e], weatherType);
        }
    }

    // animate the icons
    icons.play();
}

/** builds simple graph using chartjs.org lib **/
function buildLineGraph() {
    var ctx = document.getElementById("hourlyWeatherChart");

    // creating an empty array to store hours in it
    var allHours = [];
    // retrieving all times from the page:
    $("span.hoursMinutes").each(function () {
        // append values to an array
        allHours.push($(this).text());
    });

    // creating an empty array to store temperatures in it
    var allHourlyTemps = [];
    // retrieving all times from the page:
    $("span.currentTemp").each(function () {
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
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}

/** retrieves recent tweets **/

TWEETS = {
    // Setting: //
    // amount of tweets to display
    numTweets: 10,
    // where in DOM to append created HTML with all tweets
    appendTo: '#twitter',
    // make tweets looking awesome
    useGridalicious: false,
    // simple HTML block
    template: '<div class="item">{IMG}\
                    <div class="tweet-wrapper">\
                    <span class="text">{TEXT}</span>\
                    <span class="time">\
                        <a href="{URL}" target="_blank">{AGO}</a>\
                    </span>\
                    by <span class="user">{USER}</span>\
                    </div>\
                    </div>',

    // this function loads retrieves tweets from the backend
    // https://dev.twitter.com/docs/using-search
    loadTweets: function () {

        var request_stm;
        var request_mtl;

        request_stm = {
            q:
             "q=from:DCV_Montreal+OR+from:stm_Verte+OR+from:stm_Orange+OR+from:stm_Jaune+%23stminfo+since:"
             + getTodayDate(),
            block: "twitter"
        }

        request_mtl = {
            q:
            "q=from:DCV_Montreal+OR+from:factretriever+since:" + getTodayDate(),
            block: "twitter"
        }

        $.ajax({
            async: false,
            url: '../app/core/ajax.php',
            type: 'POST',
            dataType: 'json',
            data: request_stm,
            success: function (data, textStatus, xhr) {
                //console.log('Retrieved Tweets:');
                //console.log(data);
                if (xhr.status == 200) {

                    // clearing previous entries
                    $(TWEETS.appendTo).html('');

                    var text, name, img;

                    try {
                        // append tweets into page
                        for (var i = 0; i < TWEETS.numTweets; i++) {

                            // exiting loop in case if received amount of tweets is less than wanted to display
                            if (i == Object.keys(data.statuses).length) break;

                            img = '';
                            url =
                                'http://twitter.com/' + data.statuses[i].user.screen_name + '/status/' + data.statuses[i].id_str;
                            //try {
                            //    if (data[i].entities['media']) {
                            //        img =
                            //            '<a href="' + url + '" target="_blank"><img src="' +
                            // data.statuses[i].entities['media'][0].media_url + '" /></a>'; } } catch (e) {
                            // alert('no media'); }

                            $(TWEETS.appendTo)
                                .append(TWEETS.template.replace('{TEXT}', TWEETS.ify.cleanTweet(data.statuses[i].text))
                                    .replace('{USER}', data.statuses[i].user.screen_name)
                                    .replace('{IMG}', img)
                                    .replace('{AGO}', TWEETS.timeAgo(data.statuses[i].created_at))
                                    .replace('{URL}', url)
                                );
                        }

                    } catch (e) {
                      //  alert('item is less than item count');
                    }

                    if (TWEETS.useGridalicious) {
                        //run grid-a-licious
                        $(TWEETS.appendTo).gridalicious({
                            gutter: 13,
                            width: 200,
                            animate: true
                        });
                    }

                    // before exiting from this function - coloring tweets:
                    colorSTMUsers();

                } else console.log('Error with Twitter data fetching!'); //alert('Error with Twitter data fetching!');

            }

        }).then(function(){
            return $.ajax({
                async: false,
                url: '../app/core/ajax.php',
                type: 'POST',
                dataType: 'json',
                data: request_mtl,
                success: function (data, textStatus, xhr) {

                    if (xhr.status == 200) {

                        var text, name, img;

                        try {
                            // append tweets into page
                            for (var i = 0; i < TWEETS.numTweets; i++) {

                                // exiting loop in case if received amount of tweets is less than wanted to display
                                if (i == Object.keys(data.statuses).length) break;

                                img = '';
                                url =
                                    'http://twitter.com/' + data.statuses[i].user.screen_name + '/status/' + data.statuses[i].id_str;

                                $(TWEETS.appendTo)
                                    .append(TWEETS.template.replace('{TEXT}', TWEETS.ify.cleanTweet(data.statuses[i].text))
                                        .replace('{USER}', data.statuses[i].user.screen_name)
                                        .replace('{IMG}', img)
                                        .replace('{AGO}', TWEETS.timeAgo(data.statuses[i].created_at))
                                        .replace('{URL}', url)
                                    );
                            }

                        } catch (e) {
                            //  alert('item is less than item count');
                        }

                        if (TWEETS.useGridalicious) {
                            //run grid-a-licious
                            $(TWEETS.appendTo).gridalicious({
                                gutter: 13,
                                width: 200,
                                animate: true
                            });
                        }

                    } else console.log('Error with Twitter data fetching!') // alert('Error with Twitter data fetching!');

                }

            });
        });

        console.log('tweet refreshed!');
    },

    /**
     * relative time calculator FROM TWITTER
     * @param {string} twitter date string returned from Twitter API
     * @return {string} relative time like "2 minutes ago"
     */
    timeAgo: function (dateString) {
        var rightNow = new Date();
        var then = new Date(dateString);

        var diff = rightNow - then;

        var second = 1000,
            minute = second * 60,
            hour = minute * 60,
            day = hour * 24,
            week = day * 7;

        if (isNaN(diff) || diff < 0) {
            return ""; // return blank string if unknown
        }

        if (diff < second * 2) {
            // within 2 seconds
            return "right now";
        }

        if (diff < minute) {
            return Math.floor(diff / second) + " seconds ago";
        }

        if (diff < minute * 2) {
            return "about 1 minute ago";
        }

        if (diff < hour) {
            return Math.floor(diff / minute) + " minutes ago";
        }

        if (diff < hour * 2) {
            return "about 1 hour ago";
        }

        if (diff < day) {
            return Math.floor(diff / hour) + " hours ago";
        }

        if (diff > day && diff < day * 2) {
            return "yesterday";
        }

        if (diff < day * 365) {
            return Math.floor(diff / day) + " days ago";
        }

        else {
            return "over a year ago";
        }
    }, // timeAgo()

    /**
     * The Twitalinkahashifyer!
     * http://www.dustindiaz.com/basement/ify.html
     * Eg:
     * ify.clean('your tweet text');
     */
    ify: {
        link: function (tweet) {
            return tweet.replace(/\b(((https*\:\/\/)|www\.)[^\"\']+?)(([!?,.\)]+)?(\s|$))/g,
                function (link, m1, m2, m3, m4) {
                    var http = m2.match(/w/) ? 'http://' : '';
                    return '<a class="twtr-hyperlink" target="_blank" href="' + http + m1 + '">' + ((m1.length > 25) ? m1.substr(
                            0, 24) + '...' : m1) + '</a>' + m4;
                });
        },

        at: function (tweet) {
            return tweet.replace(/\B[@＠]([a-zA-Z0-9_]{1,20})/g, function (m, username) {
                return '<a target="_blank" class="twtr-atreply" href="http://twitter.com/intent/user?screen_name=' + username + '">@' + username + '</a>';
            });
        },

        list: function (tweet) {
            return tweet.replace(/\B[@＠]([a-zA-Z0-9_]{1,20}\/\w+)/g, function (m, userlist) {
                return '<a target="_blank" class="twtr-atreply" href="http://twitter.com/' + userlist + '">@' + userlist + '</a>';
            });
        },

        hash: function (tweet) {
            return tweet.replace(/(^|\s+)#(\w+)/gi, function (m, before, hash) {
                return before + '<a target="_blank" class="twtr-hashtag" href="http://twitter.com/search?q=%23' + hash + '">#' + hash + '</a>';
            });
        },

        clean: function (tweet) {
            return this.hash(this.at(this.list(this.link(tweet))));
        },

        removeUrls: function (tweet) {
            return tweet.replace(/(?:https?|ftp):\/\/[\n\S]+/g, '');
        },

        removeHashTags: function (tweet) {
            var regexp = new RegExp('#([^\\s]*)','g');
            return tweet.replace(regexp, '');
        },

        removeColon: function (tweet) {
            return tweet.trim().replace(/:$/g, '');
        },

        cleanTweet: function (tweet) {
            return this.removeColon(this.removeHashTags(this.removeUrls(tweet)));
        }
    } // ify

};


/** retrieves picture **/

function getPicture() {
    $.ajax({
        async: false,
        type: "GET",
        url: "../app/core/ajax.php",
        data: "block=photoFrame",
        success: function (html) {
            // clearing previous entries
            $("#photoFrame").empty();
            $("#photoFrame").html('');

            $("#photoFrame").html(html);
        }
    });
}


/** load calendar **/

function getCalendar() {

    /*
     date store today date.
     d store today date.
     m store current month.
     y store current year.
     */
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    /*
     Initialize fullCalendar and store into variable.
     Why in variable?
     Because doing so we can use it inside other function.
     In order to modify its option later.
     */

    var calendar = $('#calendar').fullCalendar(
        {
            /*
             Header option will define our calendar header.
             left define what will be at left position in calendar
             center define what will be at center position in calendar
             right define what will be at right position in calendar
             */
            height: 585,
            header: {
                left: '',
                center: '',
                right: ''
            },
            views: {
                agendaThreeDay: {
                    type: 'agenda',
                    duration: {days: 3}
                }
            },
            /*
             defaultView option used to define which view to show by default,
             for example we have used agendaWeek.
             */
            defaultView: 'agendaThreeDay',
            allDaySlot: false,
            slotDuration: '01:00:00',
            displayEventTime: false,
            eventSources: [
                {
                    //cache: true,
                    async: true,
                    url: "../app/core/ajax.php",
                    type: 'GET',
                    data: {
                        block: 'calendar'
                    },
                    error: function () {
                        console.log('There was an error while fetching events!');
                        //alert('there was an error while fetching events!');
                    }
                },

                {
                    events: [
                        //{
                        //    title: 'Meeting',
                        //    start: new Date(y, m, d, 16, 30),
                        //    allDay: false
                        //},
                        //{
                        //    title: 'Lunch',
                        //    start: new Date(y, m, d, 12, 0),
                        //    end: new Date(y, m, d, 14, 0),
                        //    allDay: false
                        //},
                        //{
                        //    title: 'Birthday Party',
                        //    start: new Date(y, m, d + 1, 19, 0),
                        //    end: new Date(y, m, d + 1, 22, 30),
                        //    allDay: false
                        //}
                    ]
                }
            ]
        });

    ////// TESTING getting CALENDAR EVENTS from GOOGLE Calendar //////

    //$.ajax({
    //    async: false,
    //    type: "GET",
    //    url: "../app/core/ajax.php",
    //    data: "block=calendar",
    //    success: function (json) {
    //        $("#calendarEventsTest").html(json);
    //    }
    //});

}


/** HELPER FUNCTIONS **/

/** reduces font size **/
function fixWeatherConditionsLength() {
    // looping through each div with class 'conditions'
    $('p.conditions').each(function () {
        // if length of the string is more than 14 chars, it will get hidden, so need to apply fix below
        if ($(this).text().length > 14) {
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
    $('p.hourlyConditions').each(function () {
        // if length of the string is more than 13 chars, it will get hidden, so need to apply fix below
        if ($(this).text().length > 13) {
            // getting current div height
            var hourlyConditionsHeight = $(this).height();
            // calculating new height
            var newHourlyCondHeight = hourlyConditionsHeight * 2 + 3;
            //console.log(newHourlyCondHeight);
            $('p.hourlyConditions').height(newHourlyCondHeight);
            $('p.hourlyConditions').css({'white-space': 'pre-wrap'});
            // need to break the loop since it's enough to apply css only once
            return false;
        }
    });
}

function getTodayDate() {
    //console.log(moment().format('YYYY-MM-DD'));
    // return moment().add(-20, 'days').format('YYYY-MM-DD');
    return moment().format('YYYY-MM-DD');
}

function colorSTMUsers() {
    // looping through each div with class '.user'
    $('#twitter .user').each(function () {
        if ($(this).text() === 'stm_Verte') {
            $(this).css({'color': 'green'});
        } else if ($(this).text() === 'stm_Orange') {
            $(this).css({'color': 'orange'});
        } else if ($(this).text() === 'stm_Jaune') {
            $(this).css({'color': 'yellow'});
        }
    });

}

/////////////// setting timeouts and intervals ///////////////
window.setInterval(function () {
    TWEETS.loadTweets();
}, 60000); // 1 minute

window.setInterval(function () {
    meteo();
}, 600000); // 10 minutes

window.setInterval(function () {
    getPicture();
}, 300000); // 5 minutes

// Essential to run 'destroy' and re-render funstions in the sequence. Using timeouts.
window.setInterval(function () {
    window.setTimeout(function() {
        $('#calendar').fullCalendar('destroy');
        // set another timeout once the first completes
        window.setTimeout(function() {
            getCalendar();
        }, 1000);
    }, 1000);

}, 3600000); // 1 hour

window.setInterval(function () {
    // re-fetching all events using .fullCalendar('refetchEvents')
    $('#calendar').fullCalendar('refetchEvents');
}, 900000); // 15 minutes













