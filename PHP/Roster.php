<?php

require_once 'AssistanceInput.php';
require_once 'Team.php';
require_once 'MonthPlan.php';


class Roster
{
    private $month;
    private $year;
    private $daysPerMonth;
    private $lastChange;
    private $servicePerson = array();
    private $standbyPerson = array();

    private $team;
    private $assistanceInput;
    private $monthPlan;

    function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
        $this->daysPerMonth = date("t", mktime(0, 0, 0, $month, 1, $year));

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $this->servicePerson[$i] = "";
            $this->standbyPerson[$i] = "";
        }

        $this->readFromFile("../Data/Roster/" . $year . "-" . $month . ".txt");

        $this->team = new Team();
        $this->assistanceInput = new AssistanceInput($year, $month);
        $this->monthPlan = new MonthPlan($year, $month);
    }

    private function readFromFile($fileName)
    {
        if (file_exists($fileName)) {
            $file = fopen($fileName, "r");

            $this->lastChange = rtrim(fgets($file));

            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $line = rtrim(fgets($file));
                $lineContent = explode(";", $line);
                $this->servicePerson[$i] = $lineContent[0];
                $this->standbyPerson[$i] = $lineContent[1];
            }

        }
    }

    private function printTableHeader($first)
    {
        echo '<tr';
        if ($first) {
            echo ' class="rosterData"';
        }
        echo '>';
        echo '<th>Datum</th>';
        echo '<th>Zeit</th>';
        echo '<th class="hidden">Dienst</th>';
        echo '<th class="hidden">Bereitschaft</th>';

        foreach ($this->assistanceInput->assistanceInput as $x => $x_value) {
            echo "<th>" . $x . "</th>";
        }

        echo '<th>Bemerkungen (öffentlich)</th>';
        echo '<th>Bemerkungen (privat)</th>';

        echo '</tr>';
    }

    private function printDayRow($day)
    {
        echo '<tr class="rosterData">';
        echo '<td class="date">' . get_short_date($this->year, $this->month, $day->dayNumber) . '</td>';
        echo '<td>' . $day->getWorkingHours() . '</td>';
        echo '<td class="hidden">' . $day->serviceHours . '</td>';
        echo '<td class="hidden">' . $day->standbyHours . '</td>';

        foreach ($this->assistanceInput->assistanceInput as $x => $x_value) {
            $allDates = explode(';', $x_value);

            $className = "";
            $cellTextContent = "";
            if (in_array($day->dayNumber, $allDates)) {
                $className = "good";
                if ($x == $this->standbyPerson[$day->dayNumber]) {
                    $className = "standby";
                    $cellTextContent = "Bereitschaft";
                }

                if ($x == $this->servicePerson[$day->dayNumber]) {
                    $className = "service";
                    $cellTextContent = "Dienst";
                }

            } else {
                $className = "bad";
            }

            echo '<td onclick="entryClicked(this)" class="' . $className . '">' . $cellTextContent . '</td>';
        }

        echo '<td class="left">' . $day->publicNotes . '</td>';
        echo '<td class="left">' . $day->privateNotes . '</td>';

        echo '</tr>';

    }

    public function printTable()
    {
        echo '<h1>Dienstplan für ' . get_month_description($this->month) . ' ' . $this->year . '</h1>';

        echo 'Letze Änderung: ' . $this->lastChange . '<br />';

        echo '<table id="rosterTable">';

        $this->printTableHeader(true);

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $this->printDayRow($this->monthPlan->days[$i]);
            if ($this->monthPlan->days[$i]->weekday == 7 && $i < $this->daysPerMonth) {
                $this->printTableHeader(false);
            }

        }

        echo '</table>';
    }

    public function printHourTable()
    {
        echo '<h1>Stundenübersicht</h1>';

        echo '<table id="hourTable">';

        echo '<tr>';
        echo '<th>Person</th>';
        echo '<th>Stunden</th>';
        echo '<th>Benötigte Stunden</th>';
        echo '<th>Differenz in Stunden</th>';
        echo '<th>Differenz in Prozent</th>';
        echo '</tr>';

        $teamHours = $this->team->getHours();

        foreach ($this->assistanceInput->assistanceInput as $x => $x_value) {
            echo '<tr>';
            echo '<td class="left">' . $x . "</td>";
            echo '<td class="right" id="hours' . $x . '"></td>';
            echo '<td class="right">';
            if (array_key_exists($x, $teamHours)) {
                echo $teamHours[$x];
            } else {
                echo '0';
            }
            echo '</td>';
            echo '<td class="right"></td>';
            echo '<td class="right"></td>';
            echo '</tr>';
        }


        echo '</table>';
    }
}

?>