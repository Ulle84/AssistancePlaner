<?php

require_once 'functions.php';

class WorkingTimes
{
    public $begin = array();
    public $end = array();

    function __construct()
    {
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

    public function readFromFile($fileName)
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
            echo '<td><input value="' . $this->begin[$i] . '" type="text" size="5" maxlength="5" /></td>';
            echo '<td><input value="' . $this->end[$i] . '" type="text" size="5" maxlength="5" /></td>';
            echo '</tr>';

        }

        echo '</table>';
    }
}

?>