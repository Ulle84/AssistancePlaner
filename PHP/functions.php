<?php
// set of functions - Don't repeat yourself!

function generate_header($month, $year) {
    $string = "<h1>";

    switch($month)
    {
        case 1:
            $string .= "Januar";
            break;
        case 2:
            $string .= "Februar";
            break;
        case 3:
            $string .= "März";
            break;
        case 4:
            $string .= "April";
            break;
        case 5:
            $string .= "Mai";
            break;
        case 6:
            $string .= "Juni";
            break;
        case 7:
            $string .= "Juli";
            break;
        case 8:
            $string .= "August";
            break;
        case 9:
            $string .= "September";
            break;
        case 10:
            $string .= "Oktober";
            break;
        case 11:
            $string .= "November";
            break;
        case 12:
            $string .= "Dezember";
            break;
    }

    $string .= " " . $year . "</h1>";

    return $string;
}

function get_weekday_description ($weekday) {
    $description = "";

    switch($weekday)
    {
        case 1:
            $description = "Mo";
            break;
        case 2:
            $description = "Di";
            break;
        case 3:
            $description = "Mi";
            break;
        case 4:
            $description = "Do";
            break;
        case 5:
            $description = "Fr";
            break;
        case 6:
            $description = "Sa";
            break;
        case 7:
            $description = "So";
            break;
    }

    return $description;
}

function get_short_date ($year, $month, $day) {
    $shortDate = get_weekday_description(date("N", mktime(0, 0, 0, $month, $day, $year)));

    $shortDate .=  ", " . date("d.m.", mktime(0, 0, 0, $month, $day, $year));

    return $shortDate;
}

?>