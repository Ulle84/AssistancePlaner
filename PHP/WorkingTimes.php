<?php

require_once 'functions.php';

class WorkingTimes
{
    public $begin = array();
    public $end = array();

    public $startTimes = array("08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00");
    public $endTimes = array("08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00");


    function __construct()
    {
        $fileName = "../Data/" . strtolower($_SESSION['clientName']) . "/Organization/defaultTimes.txt";
        if (file_exists($fileName)) {
            $this->readFromFile($fileName);
        } else {

            $this->begin[1] = "17:00";
            $this->begin[2] = "13:00";
            $this->begin[3] = "17:00";
            $this->begin[4] = "13:00";
            $this->begin[5] = "14:00";
            $this->begin[6] = "13:00";
            $this->begin[7] = "13:00";

            $this->end[1] = "08:00";
            $this->end[2] = "08:00";
            $this->end[3] = "08:00";
            $this->end[4] = "08:00";
            $this->end[5] = "13:00";
            $this->end[6] = "13:00";
            $this->end[7] = "08:00";
        }
    }

    private function readFromFile($fileName)
    {
        if (!file_exists($fileName)) {
            return;
        }

        $file = fopen($fileName, "r");

        for ($i = 1; $i <= 7; $i++) {
            $times = fgets($file);
            $this->begin[$i] = substr($times, 0, 5);
            $this->end[$i] = substr($times, 8, 5);
        }
        fclose($file);
    }

    public function printTable()
    {
        echo '<h1>Standard-Dienstzeiten</h1>';

        echo '<table id="defaultTimes">';
        echo '<tr>';
        echo '<th>Wochentag</th>';
        echo '<th>Dienstbeginn</th>';
        echo '<th>Dienstende (am Folgetag)</th>';
        echo '</tr>';

        $weekdays = get_weekdays();

        for ($i = 1; $i <= 7; $i++) {
            echo '<tr>';
            echo '<td>' . $weekdays[$i - 1] . '</td>';

            /*
             * echo '<select onchange="onStartTimeHourChanged(this)" size="1">';
        $startTime = explode(":", $day->serviceBegin);
        for ($i = 0; $i < 24; $i++) {
            $hour = "";
            if ($i < 10) {
                $hour = "0";
            }
            $hour .= $i;

            echo '<option';
            if ($startTime[0] == $hour) {
                echo ' selected="selected"';
            }
            echo '>' . $hour . '</option>';
        }
        echo '</select>';

        echo '<select onchange="onStartTimeMinuteChanged(this)" size="1">';
        for ($i = 0; $i < 60; $i += 15) {
            $minute = "";
            if ($i < 10) {
                $minute = "0";
            }
            $minute .= $i;

            echo '<option';
            if ($startTime[1] == $minute) {
                echo ' selected="selected"';
            }
            echo '>' . $minute . '</option>';
        }
        echo '</select>';

        echo '</td><td style="min-width: 120px">';

        echo '<select onchange="onEndTimeHourChanged(this)" size="1">';
        $endTime = explode(":", $day->serviceEnd);
        for ($i = 0; $i < 24; $i++) {
            $hour = "";
            if ($i < 10) {
                $hour = "0";
            }
            $hour .= $i;

            echo '<option';
            if ($endTime[0] == $hour) {
                echo ' selected="selected"';
            }
            echo '>' . $hour . '</option>';
        }
        echo '</select>';

        echo '<select onchange="onEndTimeMinuteChanged(this)" size="1">';
        for ($i = 0; $i < 60; $i += 15) {
            $minute = "";
            if ($i < 10) {
                $minute = "0";
            }
            $minute .= $i;

            echo '<option';
            if ($endTime[1] == $minute) {
                echo ' selected="selected"';
            }
            echo '>' . $minute . '</option>';
        }
        echo '</select>';

             */


            echo '<td>';

            echo '<select onchange="saveWorkingTimes()" size="1">';

            $startTime = explode(":", $this->begin[$i]);
            for ($j = 0; $j < 24; $j++) {
                $hour = "";
                if ($j < 10) {
                    $hour = "0";
                }
                $hour .= $j;

                echo '<option';
                if ($startTime[0] == $hour) {
                    echo ' selected="selected"';
                }
                echo '>' . $hour . '</option>';
            }
            echo '</select>';

            echo '<select onchange="saveWorkingTimes()" size="1">';
            for ($j = 0; $j < 60; $j += 15) {
                $minute = "";
                if ($j < 10) {
                    $minute = "0";
                }
                $minute .= $j;

                echo '<option';
                if ($startTime[1] == $minute) {
                    echo ' selected="selected"';
                }
                echo '>' . $minute . '</option>';
            }
            echo '</select>';

            echo '</td><td>';

            echo '<select onchange="saveWorkingTimes()" size="1">';
            $endTime = explode(":", $this->end[$i]);
            for ($j = 0; $j < 24; $j++) {
                $hour = "";
                if ($j < 10) {
                    $hour = "0";
                }
                $hour .= $j;

                echo '<option';
                if ($endTime[0] == $hour) {
                    echo ' selected="selected"';
                }
                echo '>' . $hour . '</option>';
            }
            echo '</select>';

            echo '<select onchange="saveWorkingTimes()" size="1">';
            for ($j = 0; $j < 60; $j += 15) {
                $minute = "";
                if ($j < 10) {
                    $minute = "0";
                }
                $minute .= $j;

                echo '<option';
                if ($endTime[1] == $minute) {
                    echo ' selected="selected"';
                }
                echo '>' . $minute . '</option>';
            }
            echo '</select>';
            echo '</td>';
            echo '</tr>';

        }
        echo '</table>';
    }
}

?>