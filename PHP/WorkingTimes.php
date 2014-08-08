<?php

require_once 'functions.php';

class WorkingTimes
{
    public $begin = array();
    public $end = array();

    public $startTimes = array("12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30");
    public $endTimes = array("08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30");


    function __construct()
    {
        $fileName = "../Data/" . $_SESSION['clientName'] . "/Organization/defaultTimes.txt";
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
        echo '<th>Dienstende</th>';
        echo '</tr>';

        $weekdays = get_weekdays();

        for ($i = 1; $i <= 7; $i++) {
            echo '<tr>';
            echo '<td>' . $weekdays[$i - 1] . '</td>';
            echo '<td>';

            echo '<select size="1">';

            foreach ($this->startTimes as $startTime) {
                echo '<option';
                if ($startTime == $this->begin[$i]) {
                    echo ' selected="selected"';
                }
                echo '>' . $startTime . '</option>';
            }

            echo '</select>';

            echo '</td>';
            echo '<td>';
            echo '<select size="1">';

            foreach ($this->endTimes as $endTime) {
                echo '<option';
                if ($endTime == $this->end[$i]) {
                    echo ' selected="selected"';
                }
                echo '>' . $endTime . '</option>';
            }

            echo '</select>';

            echo '</td>';
            echo '</tr>';

        }
        echo '</table>';
    }
}

?>