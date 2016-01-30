<?php
// set of functions - Don't repeat yourself!

function generate_header($month, $year)
{
    return "<h1>" . get_month_description($month) . " " . $year . "</h1>";
}

function get_month_description($month)
{
    $string = "";
    switch ($month) {
        case 1:
            $string = "Januar";
            break;
        case 2:
            $string = "Februar";
            break;
        case 3:
            $string = "März";
            break;
        case 4:
            $string = "April";
            break;
        case 5:
            $string = "Mai";
            break;
        case 6:
            $string = "Juni";
            break;
        case 7:
            $string = "Juli";
            break;
        case 8:
            $string = "August";
            break;
        case 9:
            $string = "September";
            break;
        case 10:
            $string = "Oktober";
            break;
        case 11:
            $string = "November";
            break;
        case 12:
            $string = "Dezember";
            break;
    }
    return $string;
}

function get_weekday_description($weekday)
{
    $weekdays = get_weekdays();

    return $weekdays[$weekday - 1];
}

function get_weekdays()
{
    $weekdays = array();
    $weekdays[0] = "Mo";
    $weekdays[1] = "Di";
    $weekdays[2] = "Mi";
    $weekdays[3] = "Do";
    $weekdays[4] = "Fr";
    $weekdays[5] = "Sa";
    $weekdays[6] = "So";

    return $weekdays;
}

function get_short_date($year, $month, $day)
{
    $shortDate = get_weekday_description(date("N", mktime(0, 0, 0, $month, $day, $year)));

    $shortDate .= ", " . date("d.m.", mktime(0, 0, 0, $month, $day, $year));

    return $shortDate;
}

?>