<?php

require_once 'Day.php';
require_once 'functions.php';
require_once 'WorkingTimes.php';

class MonthPlan
{
    public $year;
    public $month;
    public $calendarId = "calendar";
    public $days = array();

    private $daysPerMonth;

    function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
        $this->daysPerMonth = date("t", mktime(0, 0, 0, $month, 1, $year));

        $defaultWorkingTimes = new WorkingTimes();

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $this->days[$i] = new Day();
            $this->days[$i]->dayNumber = $i;
        }

        $this->initWeekdays();

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {

            $fileName = "../Data/MonthPlan/" . $year . "-" . $month . ".txt";

            if (file_exists($fileName)) {
                $this->readFromFile($fileName);
            } else {
                $this->days[$i]->serviceBegin = $defaultWorkingTimes->begin[$this->days[$i]->weekday];
                $this->days[$i]->serviceEnd = $defaultWorkingTimes->end[$this->days[$i]->weekday];
            }

            $this->days[$i]->calculateWorkingHours();
        }

    }

    private function initWeekdays()
    {
        $weekday = date("N", mktime(0, 0, 0, $this->month, 1, $this->year));

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $this->days[$i]->weekday = $weekday;

            $weekday++;
            if ($weekday == 8) {
                $weekday = 1;
            }
        }
    }

    private function readFromFile($fileName)
    {
        if (file_exists($fileName)) {
            $file = fopen($fileName, "r");

            $this->days = array();

            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $day = new Day();

                $times = fgets($file);
                $day->serviceBegin = substr($times, 0, 5);
                $day->serviceEnd = substr($times, 8, 5);
                $day->publicNotes = rtrim(fgets($file));
                $day->privateNotes = rtrim(fgets($file));
                $day->dayNumber = $i;
                $day->calculateWorkingHours();

                $this->days[$i] = $day;
            }

            fclose($file);
        }

        $this->initWeekdays();
    }

    private function hasPublicNotes()
    {
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            if ($this->days[$i]->publicNotes != "") {
                return true;
            }
        }
        return false;
    }

    private function printTableHeader()
    {
        echo '<tr>';

        echo '<th>Datum</th>';
        echo '<th>Dienstbeginn</th>';
        echo '<th>Dienstende</th>';
        echo '<th>Bemerkungen (öffentlich)</th>';
        echo '<th>Bemerkungen (privat)</th>';

        echo '</tr>';
    }

    private function printDay($day)
    {
        echo '<tr class="data">';

        echo '<td class="date">' . get_short_date($this->year, $this->month, $day->dayNumber) . '</td>';
        echo '<td><input value="' . $day->serviceBegin . '" type="text" size="5" maxlength="5" /></td>';
        echo '<td><input value="' . $day->serviceEnd . '" type="text" size="5" maxlength="5" /></td>';
        echo '<td><input value="' . htmlspecialchars($day->publicNotes) . '" type="text" size="30" maxlength="200" /></td>';
        echo '<td><input value="' . htmlspecialchars($day->privateNotes) . '" type="text" size="30" maxlength="200" /></td>';

        echo '</tr>';
    }

    public function printTable()
    {
        echo '<h1>Monatsplan für ' . get_month_description($this->month) . ' ' . $this->year . '</h1>';

        echo '<table>';
        $this->printTableHeader();

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $this->printDay($this->days[$i]);
        }

        echo '</table>';
    }

    private function printCalendarHeader()
    {
        echo "<tr>";
        echo "<th>Mo</th>";
        echo "<th>Di</th>";
        echo "<th>Mi</th>";
        echo "<th>Do</th>";
        echo "<th>Fr</th>";
        echo "<th>Sa</th>";
        echo "<th>So</th>";
        echo "</tr>";
    }

    public function printCalendar()
    {
        echo '<h2>Eingabe der Daten</h2>';

        $weekday = date("N", mktime(0, 0, 0, $this->month, 1, $this->year));

        echo '<table id="' . $this->calendarId .'" >';

        $this->printCalendarHeader();

        echo "<tr>";

        $cellCounter = 0;

        // print empty cells
        for ($i = 1; $i < $weekday; $i++) {
            echo "<td></td>";
            $cellCounter++;
        }

        // print days
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            echo '<td class="bad" onclick="dateClicked(this)"><b>' . $i . '</b><br />';
            echo $this->days[$i]->serviceBegin . ' - ' . $this->days[$i]->serviceEnd . '</td>';
            $cellCounter++;

            if ($cellCounter % 7 == 0 && $i != $this->daysPerMonth) {
                echo "</tr><tr>";
            }
        }

        // print empty cells
        while ($cellCounter % 7 != 0) {
            echo "<td></td>";
            $cellCounter++;
        }

        echo "</tr>";
        echo "</table>";
    }

    public function printHeader()
    {
        echo generate_header($this->month, $this->year);
    }

    public function printPublicNotes()
    {
        if ($this->hasPublicNotes()) {
            echo '<h2>Bemerkungen</h2>';
            echo '<table>';

            echo "<tr>";
            echo "<th>Datum</th>";
            echo "<th>Bemerkung</th>";
            echo "</tr>";

            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                if ($this->days[$i]->publicNotes != "") {
                    echo "<tr>";
                    echo '<td class="date">' . get_short_date($this->year, $this->month, $i) . '</td>';
                    echo '<td class="left">' . $this->days[$i]->publicNotes . '</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
        }
    }
}

?>