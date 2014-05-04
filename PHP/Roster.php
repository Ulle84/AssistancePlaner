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
    private $rosterExist = false;

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

        $this->team = new Team();
        $this->assistanceInput = new AssistanceInput($year, $month);
        $this->monthPlan = new MonthPlan($year, $month);

        $this->readFromFile("../Data/Roster/" . $year . "-" . $month . ".txt");

        if (!$this->rosterExist) {
            $this->createRoster();
        }
    }

    private function readFromFile($fileName)
    {
        if (file_exists($fileName)) {
            $this->rosterExist = true;

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

    private function writeToFile()
    {
        $fileName = "../Data/Roster/" . $this->year . "-" . $this->month . ".txt";
        $fh = fopen($fileName, "w");
        fwrite($fh, date("d.m.Y H:i\n"));

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            fwrite($fh, $this->servicePerson[$i] . ';' . $this->standbyPerson[$i] . "\n");
        }

        fclose($fh);
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

        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
            echo "<th>" . $name . "</th>";
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

        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
            $className = "";
            $cellTextContent = "";
            if ($dates[$day->dayNumber - 1] == 1) {
                $className = "good";
                if ($name == $this->standbyPerson[$day->dayNumber]) {
                    $className = "standby";
                    $cellTextContent = "Bereitschaft";
                }

                if ($name == $this->servicePerson[$day->dayNumber]) {
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

    private function createRoster()
    {
        $this->createRosterAlgorithm4();
    }

    private function createRosterAlgorithm1()
    {
        // strategy: give the first available the service and the second available the standby

        if (!$this->assistanceInput->dataExist) {
            return;
        }

        // check that there is enough availability
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $sum = 0;
            foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
                $sum += $dates[$i - 1];
            }
            if ($sum < 2) {
                return;
            }
        }

        // set service and standby
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $serviceTaken = false;
            $standbyTaken = false;
            foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
                if ($dates[$i - 1] == 1) {
                    if (!$serviceTaken) {
                        $this->servicePerson[$i] = $name;
                        $serviceTaken = true;
                        continue;
                    }
                    if (!$standbyTaken) {
                        $this->standbyPerson[$i] = $name;
                        $standbyTaken = true;
                        continue;
                    }
                }
            }
        }

        //$this->writeToFile();
    }

    private function createRosterAlgorithm2()
    {
        // strategy give the best rated the service and the second best rated the standby

        if (!$this->assistanceInput->dataExist) {
            return;
        }

        // check that there is enough availability
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $sum = 0;
            foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
                $sum += $dates[$i - 1];
            }
            if ($sum < 2) {
                return;
            }
        }

        // calculate score table
        $priorities = $this->team->getPriorities();
        $scoreTable = $this->assistanceInput->assistanceInput;

        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $scoreTable[$name][$i - 1] *= $priorities[$name];
            }
        }

        $this->printScoreTable($scoreTable);

        // set service and standby
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $scores = array();
            foreach ($scoreTable as $name => $dates) {
                if ($dates[$i - 1] > 0) {
                    array_push($scores, $dates[$i - 1]);
                }
            }
            rsort($scores);

            $serviceTaken = false;
            $standbyTaken = false;
            foreach ($scoreTable as $name => $dates) {
                if ($dates[$i - 1] == $scores[0] && !$serviceTaken) {
                    $serviceTaken = true;
                    $this->servicePerson[$i] = $name;
                    continue;
                }

                if ($dates[$i - 1] == $scores[1] && !$standbyTaken) {
                    $standbyTaken = true;
                    $this->standbyPerson[$i] = $name;
                    continue;
                }
            }
        }

        //$this->writeToFile();
    }

    private function createRosterAlgorithm3()
    {
        // strategy give the best rated the service and the second best rated the standby
        // look also for preferred weekdays

        if (!$this->assistanceInput->dataExist) {
            return;
        }

        // check that there is enough availability
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $sum = 0;
            foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
                $sum += $dates[$i - 1];
            }
            if ($sum < 2) {
                return;
            }
        }

        // calculate score table
        $priorities = $this->team->getPriorities();
        $scoreTable = $this->assistanceInput->assistanceInput;
        $preferredWeekdays = $this->team->getPreferredWeekdays();

        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $scoreTable[$name][$i - 1] *= $priorities[$name];
                if ($preferredWeekdays[$name][$this->monthPlan->days[$i]->weekday - 1] == 1) {
                    $scoreTable[$name][$i - 1] *= 2;
                }
            }
        }

        $this->printScoreTable($scoreTable);

        // set service and standby
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $scores = array();
            foreach ($scoreTable as $name => $dates) {
                if ($dates[$i - 1] > 0) {
                    array_push($scores, $dates[$i - 1]);
                }
            }
            rsort($scores);

            $serviceTaken = false;
            $standbyTaken = false;
            foreach ($scoreTable as $name => $dates) {
                if ($dates[$i - 1] == $scores[0] && !$serviceTaken) {
                    $serviceTaken = true;
                    $this->servicePerson[$i] = $name;
                    continue;
                }

                if ($dates[$i - 1] == $scores[1] && !$standbyTaken) {
                    $standbyTaken = true;
                    $this->standbyPerson[$i] = $name;
                    continue;
                }
            }
        }

        //$this->writeToFile();
    }

    private function createRosterAlgorithm4()
    {
        // strategy give the best rated the service and the second best rated the standby
        // look also for preferred weekdays
        // look also for the quota of hours

        if (!$this->assistanceInput->dataExist) {
            return;
        }

        // check that there is enough availability
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $sum = 0;
            foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
                $sum += $dates[$i - 1];
            }
            if ($sum < 2) {
                return;
            }
        }

        // calculate complete quota of hours
        $totalQuotaOfHours = 0;
        $quotaOfHours = $this->team->getHours();
        foreach ($quotaOfHours as $name => $value) {
            $totalQuotaOfHours += $value;
        }
        echo 'total quota of hours: ' . $totalQuotaOfHours . '<br />';

        // calculate service and standby hours
        $totalOfServiceHours = 0;
        $totalOfStandbyHours = 0;
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $totalOfServiceHours += $this->monthPlan->days[$i]->serviceHours;
            $totalOfStandbyHours += $this->monthPlan->days[$i]->standbyHours;
        }
        echo 'total of service and standby hours: ' . ($totalOfServiceHours + $totalOfStandbyHours) . '<br />';


        // calculate score table
        $priorities = $this->team->getPriorities();
        $scoreTable = $this->assistanceInput->assistanceInput;
        $preferredWeekdays = $this->team->getPreferredWeekdays();

        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $scoreTable[$name][$i - 1] *= $priorities[$name];
                if ($preferredWeekdays[$name][$this->monthPlan->days[$i]->weekday - 1] == 1) {
                    $scoreTable[$name][$i - 1] *= 2;
                }
            }
        }

        $this->printScoreTable($scoreTable);

        // convert data
        $convertedData = array();
        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $entry = array();
                array_push($entry, $scoreTable[$name][$i - 1]);
                array_push($entry, $name);
                array_push($entry, $i);

                array_push($convertedData, $entry);
            }
        }
        rsort($convertedData);

        //TODO
        // iterate over $convertedData
        // first set all services, than set all standbys

        // set service and standby
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $scores = array();
            foreach ($scoreTable as $name => $dates) {
                if ($dates[$i - 1] > 0) {
                    array_push($scores, $dates[$i - 1]);
                }
            }
            rsort($scores);

            $serviceTaken = false;
            $standbyTaken = false;
            foreach ($scoreTable as $name => $dates) {
                if ($dates[$i - 1] == $scores[0] && !$serviceTaken) {
                    $serviceTaken = true;
                    $this->servicePerson[$i] = $name;
                    continue;
                }

                if ($dates[$i - 1] == $scores[1] && !$standbyTaken) {
                    $standbyTaken = true;
                    $this->standbyPerson[$i] = $name;
                    continue;
                }
            }
        }

        //$this->writeToFile();
    }

    private function printScoreTable($scoreTable)
    {
        echo '<table>';

        echo '<tr>';
        echo '<th>Tag</th>';
        foreach ($scoreTable as $name => $dates) {
            echo '<th>' . $name . '</th>';
        }
        echo '</tr>';

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            echo '<tr>';
            echo '<td>' . get_short_date($this->year, $this->month, $i) . '</td>';
            foreach ($scoreTable as $name => $dates) {
                echo '<td>' . $dates[$i - 1] . '</td>';
            }
            echo '</tr>';
        }

        echo '</table>';
    }
}

?>