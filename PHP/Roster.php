<?php

require_once 'TeamOrganisationInterface.php';

require_once 'AssistanceInput.php';
require_once 'Team.php';
require_once 'MonthPlan.php';
require_once 'Day.php';
require_once 'functions.php';
require_once 'WorkingTimes.php';
require_once 'Settings.php';

require_once '../ExternalResources/FreePDF_v1_7/fpdf.php';

function compare($value1, $value2)
{
    $a = $value1[0];
    $b = $value2[0];

    if ($a == $b) {
        return 0;
    }

    return ($a > $b) ? -1 : +1;
}

class Roster
{
    private $month;
    private $year;
    private $daysPerMonth;
    private $lastChange;
    private $publishedDate;
    private $closedDate;
    private $servicePerson = array();
    private $standbyPerson = array();
    private $rosterExist = false;

    private $team;
    private $assistanceInput;
    private $monthPlan;

    public $calendarId = "calendar";
    public $days = array();
    public $notes = array();
    public $defaultWorkingTimes;
    private $settings;

    function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
        $this->daysPerMonth = date("t", mktime(0, 0, 0, $month, 1, $year));

        $this->defaultWorkingTimes = new WorkingTimes();

        $this->settings = new Settings($_SESSION['clientName']);

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $this->servicePerson[$i] = "";
            $this->standbyPerson[$i] = "";
        }

        $this->team = new Team();
        $this->assistanceInput = new AssistanceInput($year, $month);

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $this->days[$i] = new Day();
            $this->days[$i]->dayNumber = $i;
        }

        $this->initWeekdays();

        $fileName = "../Data/" . strtolower($_SESSION['clientName']) . "/Roster/" . $year . "-" . $month . ".txt";

        $this->readFromFile($fileName);

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            if (!file_exists($fileName)) {
                $this->days[$i]->serviceBegin = $this->defaultWorkingTimes->begin[$this->days[$i]->weekday];
                $this->days[$i]->serviceEnd = $this->defaultWorkingTimes->end[$this->days[$i]->weekday];
                $this->days[$i]->calculateWorkingHours();
            }
        }

        /*if (!$this->rosterExist) {
            $this->createRoster();
        }*/
    }

    public function generateRoster($workingTimes)
    {
        $workingTimesArray = explode("\n", $workingTimes);

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $this->days[$i]->serviceBegin = substr($workingTimesArray[$i - 1], 0, 5);
            $this->days[$i]->serviceEnd = substr($workingTimesArray[$i - 1], 8, 5);
            $this->days[$i]->calculateWorkingHours();
        }

        $this->createRoster();

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            echo $this->servicePerson[$i] . "\n";
            echo $this->standbyPerson[$i] . "\n";
        }
    }

    private function readFromFile($fileName)
    {
        /*
         * if (file_exists($fileName)) {
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

            while (!feof($file)) {
                $line = rtrim(fgets($file));
                array_push($this->notes, $line);
            }

            fclose($file);
        }

        $this->initWeekdays();
         */


        if (file_exists($fileName)) {

            $file = fopen($fileName, "r");

            $this->lastChange = rtrim(fgets($file));
            $this->publishedDate = rtrim(fgets($file));
            $this->closedDate = rtrim(fgets($file));

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

                $this->servicePerson[$i] = rtrim(fgets($file));
                $this->standbyPerson[$i] = rtrim(fgets($file));
            }

            if ($this->servicePerson[1] != "") {
                $this->rosterExist = true;
            }

            while (!feof($file)) {
                $line = rtrim(fgets($file));
                array_push($this->notes, $line);
            }

            fclose($file);

        }

        $this->initWeekdays();
    }

    /*private function writeToFile()
    {
        $fileName = "../Data/" . $_SESSION['clientName'] . "/Roster/" . $this->year . "-" . $this->month . ".txt";
        $fh = fopen($fileName, "w");
        fwrite($fh, date("d.m.Y H:i\n"));

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            fwrite($fh, $this->servicePerson[$i] . ';' . $this->standbyPerson[$i] . "\n");
        }

        fclose($fh);
    }*/

    //TODO put compareTimes, getLatterTime, addOneHour in a helper class

    private function compareTimes($time1, $time2)
    {
        // returns 0 if equal
        // returns 1 if $time1 is before $time2
        // returns -1 if $time1 is after $time2

        if ($time1 == $time2) {
            return 0;
        }

        $hours1 = substr($time1, 0, 2);
        $minutes1 = substr($time1, 3, 2);

        $hours2 = substr($time2, 0, 2);
        $minutes2 = substr($time2, 3, 2);

        if ($hours1 < $hours2) {
            return 1;
        }

        if ($hours1 > $hours2) {
            return -1;
        }

        // here $hours1 should be equal $hours2
        if ($minutes1 < $minutes2) {
            return 1;
        } else {
            return -1;
        }
    }

    private function getLatterTime($time1, $time2)
    {
        if ($this->compareTimes($time1, $time2) == 1) {
            return $time2;
        } else {
            return $time1;
        }
    }

    private function addOneHour($time)
    {
        $hours = (int)substr($time, 0, 2);

        $hours++;
        if ($hours == 24) {
            $hours = 0;
        }

        $returnTime = "";
        if ($hours < 10) {
            $returnTime = "0";
        }
        $returnTime .= $hours . substr($time, 2, 3);

        return $returnTime;
    }

    private function getServiceHours($day)
    {
        $serviceHours = "10:00 - 11:00";

        if ($day != 1) {
            if ($this->standbyPerson[$day] == $this->servicePerson[$day - 1]) {
                $latterTime = $this->getLatterTime("10:00", $this->days[$day - 1]->serviceEnd);
                $serviceHours = $latterTime . " - " . $this->addOneHour($latterTime);
            }
        } else {
            $previousMonth = $this->month - 1;
            $year = $this->year;
            if ($previousMonth == 0) {
                $previousMonth = 12;
                $year--;
            }

            $rosterPreviousMonth = new Roster($year, $previousMonth);
            if ($rosterPreviousMonth->rosterExist) {
                $lastDayOfMonth = date("t", mktime(0, 0, 0, $previousMonth, 1, $year));
                if ($this->standbyPerson[$day] == $rosterPreviousMonth->servicePerson[$lastDayOfMonth]) {
                    $latterTime = $this->getLatterTime("10:00", $rosterPreviousMonth->monthPlan->days[$lastDayOfMonth]->serviceEnd);
                    $serviceHours = $latterTime . " - " . $this->addOneHour($latterTime);
                }
            }
        }

        if ($this->days[$day]->serviceHours > 13) {
            $serviceHours .= " und 18:00 - 19:00";
        }

        return $serviceHours;
    }

    private function printTableHeader($first)
    {
        echo '<tr';
        if ($first) {
            echo ' class="rosterData"';
        }
        echo '>';
        echo '<th>Datum</th>';
        echo '<th>Beginn</th>';
        echo '<th>Ende</th>';
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
        echo '<td class="date" style="min-width: 80px">' . get_short_date($this->year, $this->month, $day->dayNumber) . '</td>';

        echo '<td style="min-width: 120px">';
        echo '<select onchange="onStartTimeHourChanged(this); save(' . $this->year . ', ' . $this->month . ')" size="1">';
        $startTime = explode(":", $day->serviceBegin);
        $hideOptions = false;

        echo '<option';
        if ($startTime[0] == "--") {
            echo ' selected="selected"';
            $hideOptions = true;
        }
        echo '>--</option>';

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

        echo '<select onchange="onStartTimeMinuteChanged(this); save(' . $this->year . ', ' . $this->month . ')" size="1"';
        if ($hideOptions) {
            echo ' class="hidden"';
        }
        echo '>';
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

        echo '<select onchange="onEndTimeHourChanged(this); save(' . $this->year . ', ' . $this->month . ')" size="1"';
        if ($hideOptions) {
            echo ' class="hidden"';
        }
        echo '>';
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

        echo '<select onchange="onEndTimeMinuteChanged(this); save(' . $this->year . ', ' . $this->month . ')" size="1"';
        if ($hideOptions) {
            echo ' class="hidden"';
        }
        echo '>';
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

        echo '</td>';

        echo '<td class="hidden">' . $day->serviceHours . '</td>';
        echo '<td class="hidden">' . $day->standbyHours . '</td>';

        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
            $className = "";
            $baseClassName = "";
            $cellTextContent = "";
            if ($dates[$day->dayNumber - 1] > 0) {
                if ($dates[$day->dayNumber - 1] == 1) {
                    $className = "okay";
                    $baseClassName = "okay";
                }

                if ($dates[$day->dayNumber - 1] == 10) {
                    $className = "good";
                    $baseClassName = "good";
                }

            } else {
                $className = "bad";
                $baseClassName = "bad";
            }

            if ($name == $this->standbyPerson[$day->dayNumber]) {
                $className = "standby";
                $cellTextContent = "Bereitschaft";
            }

            if ($name == $this->servicePerson[$day->dayNumber]) {
                $className = "service";
                $cellTextContent = "Dienst";
            }

            echo '<td onclick="entryClicked(this); save(' . $this->year . ', ' . $this->month . ')" class="' . $className . '" baseClass="' . $baseClassName . '">' . $cellTextContent . '</td>';
        }

        /*echo '<td class="left">' . $day->publicNotes . '</td>';
        echo '<td class="left">' . $day->privateNotes . '</td>';*/
        echo '<td><input onchange="validateString(this); save(' . $this->year . ', ' . $this->month . ')" onblur="validateString(this)" value="' . htmlspecialchars($day->publicNotes) . '" type="text" size="30" maxlength="200" /></td>';
        echo '<td><input onchange="validateString(this); save(' . $this->year . ', ' . $this->month . ')" onblur="validateString(this)" value="' . htmlspecialchars($day->privateNotes) . '" type="text" size="30" maxlength="200" /></td>';

        echo '</tr>';

    }

    private function printTableBase()
    {
        echo '<h1>Dienstplan für ' . get_month_description($this->month) . ' ' . $this->year . '</h1>';
        echo '<div>';

        echo '<span';
        if ($this->lastChange == "") {
            echo ' class="hidden"';
        }
        echo '>Letze Änderung am: <span id="lastChange">' . $this->lastChange . '</span></span>';


        echo '<span';
        if ($this->publishedDate == "") {
            echo ' class="hidden"';
        }
        echo '>&nbsp;&nbsp;&nbsp;Veröffentlicht am: <span id="publishedDate">' . $this->publishedDate . '</span></span>';

        echo '<span';
        if ($this->closedDate == "") {
            echo ' class="hidden"';
        }
        echo '>&nbsp;&nbsp;&nbsp;Abgeschlossen am: <span id="closedDate">' . $this->closedDate . '</span></span>';

        echo '</div><br />';


        echo '<table id="rosterTable">';
    }

    public function printTableClient()
    {
        $this->printTableBase();

        $this->printTableHeader(true);

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $this->printDayRow($this->days[$i]);
            if ($this->days[$i]->weekday == 7 && $i < $this->daysPerMonth) {
                $this->printTableHeader(false);
            }

        }

        echo '</table>';
    }

    public function printNotesFromAssistants()
    {
        if (!$this->assistanceInput->hasNotes()) {
            return;
        }

        echo '<h1>Bemerkungen der Assistenten</h1>';
        echo '<table>';
        echo '<tr>';
        echo '<th>Name</th>';
        echo '<th>Bemerkung</th>';
        echo '</tr>';

        foreach ($this->assistanceInput->assistanceNotes as $name => $notes) {
            if ($notes != "") {
                echo '<tr>';
                echo '<td class="topLeft">' . $name . '</td>';
                echo '<td class="topLeft">' . $notes . '</td>';
                echo '</tr>';
            }
        }


        echo '</table>';
    }

    public function printTablesAssistant()
    {
        if ($this->publishedDate == "") {
            echo '<br />Der Dienstplan für den Monat ' . get_month_description($this->month) . ' ' . $this->year . ' wurde noch nicht fertiggestellt.';
            return;
        }

        $this->printTableBase();

        echo '<tr>';
        echo '<th>Datum</th>';
        echo '<th>Dienst-Zeit</th>';
        echo '<th>Dienst</th>';

        echo '<th>Bereitschafts-Zeit</th>';
        echo '<th>Bereitschaft</th>';
        echo '<th>Bemerkungen</th>';
        echo '</tr>';

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            echo '<tr';

            if ($this->servicePerson[$i] == $_SESSION['assistantName'] || $this->standbyPerson[$i] == $_SESSION['assistantName']) {
                echo ' class="highlighted"';
            }

            echo '><td class="date">' . get_short_date($this->year, $this->month, $i) . '</td>';

            if (substr($this->days[$i]->serviceBegin, 0, 2) == "--") {
                echo '<td class="left">kein Dienst</td>';
                echo '<td class="left"></td>';

                echo '<td class="left"></td>';
                echo '<td class="left"></td>';
            } else {
                echo '<td class="left">' . $this->days[$i]->getWorkingHours() . '</td>';
                echo '<td class="left">' . $this->servicePerson[$i] . '</td>';

                echo '<td class="left">' . $this->getServiceHours($i) . '</td>';
                echo '<td class="left">' . $this->standbyPerson[$i] . '</td>';
            }

            echo '<td class="left">' . $this->days[$i]->publicNotes . '</td>';


            echo '</tr>';
        }
        echo '</table>';


        echo '<h1>Meine Dienste und Bereitschaften</h1>';
        echo '<table>';

        echo '<tr>';
        echo '<th>Datum</th>';
        echo '<th>Zeit</th>';
        echo '<th>Typ</th>';
        echo '</tr>';

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            if ($this->servicePerson[$i] == $_SESSION['assistantName']) {
                echo '<tr>';
                echo '<td class="date">' . get_short_date($this->year, $this->month, $i) . '</td>';
                echo '<td class="left">' . $this->days[$i]->getWorkingHours() . '</td>';
                echo '<td class="left">Dienst</td>';
                echo '</tr>';
            }

            if ($this->standbyPerson[$i] == $_SESSION['assistantName']) {
                echo '<tr>';
                echo '<td class="date">' . get_short_date($this->year, $this->month, $i) . '</td>';

                echo '<td class="left">' . $this->getServiceHours($i) . '</td>';

                echo '<td class="left">Bereitschaft</td>';
                echo '</tr>';
            }

        }

        echo '</table>';
    }

    public function printHourTable()
    {
        if (!$this->assistanceInput->dataExist) {
            return;
        }


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

    public function printPdf()
    {
        $pdf = new FPDF('L');
        $pdf->SetTitle("Dienstplan", true);
        $header = array('Datum', 'Dienst-Zeit', 'Dienst', 'Bereitschafts-Zeit', 'Bereitschaft');
        $w = array(25, 30, 70, 65, 70);

        $pdf->SetFont('Arial', '', 12);
        $pdf->AddPage();

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(.3);

        $pdf->SetFont('', 'B');

        $settings = new Settings($_SESSION['clientName']);
        $pdf->Cell(200, 10, utf8_decode('Team ' . $settings->adminFirstName . ' ' . $settings->adminLastName . ' - Dienstplan ' . get_month_description($this->month) . ' ' . $this->year . ' - Letzte Änderung: ' . $this->lastChange));
        $pdf->Ln();

        $cellHeight = 5.3;

        for ($j = 0; $j < count($header); $j++) {
            $pdf->Cell($w[$j], $cellHeight, $header[$j], 1, 0, 'C', true);
        }
        $pdf->SetFont('');

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $pdf->Ln();


            $pdf->Cell($w[0], $cellHeight, get_short_date($this->year, $this->month, $i), 1, 0, 'R', false);

            if (substr($this->days[$i]->serviceBegin, 0, 2) == "--") {
                $pdf->Cell($w[1], $cellHeight, 'Kein Dienst', 1, 0, 'C', false);
                $pdf->Cell($w[2], $cellHeight, '', 1, 0, 'L', false);

                $pdf->Cell($w[3], $cellHeight, '', 1, 0, 'L', false);
                $pdf->Cell($w[4], $cellHeight, '', 1, 0, 'L', false);
            } else {
                $pdf->Cell($w[1], $cellHeight, $this->days[$i]->getWorkingHours(), 1, 0, 'C', false);
                $pdf->Cell($w[2], $cellHeight, utf8_decode($this->team->getFullNameFromId($this->servicePerson[$i])), 1, 0, 'L', false);

                $pdf->Cell($w[3], $cellHeight, $this->getServiceHours($i), 1, 0, 'L', false);
                $pdf->Cell($w[4], $cellHeight, utf8_decode($this->team->getFullNameFromId($this->standbyPerson[$i])), 1, 0, 'L', false);
            }
        }

        $pdf->Output();
    }

    private function createRoster()
    {
        $this->createRosterAlgorithm5();
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
                if ($dates[$i - 1] > 0) {
                    $sum++;
                }
            }
            //echo 'day: ' . $i . ' sum: ' . $sum . '<br />';
            if ($sum < 2) {
                return;
            }
        }

        // set service and standby
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $serviceTaken = false;
            $standbyTaken = false;
            foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
                if ($dates[$i - 1] > 0) {
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
        // take priority into account

        if (!$this->assistanceInput->dataExist) {
            return;
        }

        // check that there is enough availability
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $sum = 0;
            foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
                if ($dates[$i - 1] > 0) {
                    $sum++;
                }
            }
            //echo 'day: ' . $i . ' sum: ' . $sum . '<br />';
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

        //$this->printScoreTable($scoreTable);

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
        // take priority into account
        // take preferred weekdays into account

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
                if ($preferredWeekdays[$name][$this->days[$i]->weekday - 1] == 1) {
                    $scoreTable[$name][$i - 1] *= 2;
                }
            }
        }

        //$this->printScoreTable($scoreTable);

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
        // take priority into account
        // take preferred weekdays into account
        // take keywords (in the month plan) into account

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
        $keyWords = $this->team->getKeyWords();

        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $scoreTable[$name][$i - 1] *= $priorities[$name];
                if ($preferredWeekdays[$name][$this->days[$i]->weekday - 1] == 1) {
                    $scoreTable[$name][$i - 1] *= 2;
                }
                foreach ($keyWords[$name] as $keyWord) {
                    if ($keyWord != "") {
                        if (strpos(strtolower($this->days[$i]->privateNotes), strtolower($keyWord)) !== false) {
                            $scoreTable[$name][$i - 1] *= 10;
                        }
                    }
                }
            }
        }

        //$this->printScoreTable($scoreTable);

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

    private function createRosterAlgorithm5()
    {
        // take priority into account
        // take preferred weekdays into account
        // take keywords (in the month plan) into account
        // take working hours into account
        // generate score table and sort
        // generate a lot of rosters and take the best in the end

        if (!$this->assistanceInput->dataExist) {
            return;
        }

        // check that there is enough availability
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            if (substr($this->days[$i]->serviceBegin, 0, 2) == "--") {
                // no service needed
                continue;
            }

            $sum = 0;
            foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
                if ($dates[$i - 1] > 0) {
                    $sum++;
                }
            }
            //echo 'day: ' . $i . ' sum: ' . $sum . '<br />';
            if ($sum < 2) {
                return;
            }
        }

        // calculate complete quota of hours
        $totalQuotaOfHours = 0;
        $quotaOfHours = $this->team->getHours();
        foreach ($quotaOfHours as $name => $value) {
            if (array_key_exists($name, $this->assistanceInput->assistanceInput)) {
                // only add value, if assistant has provided information for this month
                $totalQuotaOfHours += $value;
            }
        }

        // calculate service and standby hours
        $totalOfServiceHours = 0;
        $totalOfStandbyHours = 0;
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $totalOfServiceHours += $this->days[$i]->serviceHours;
            $totalOfStandbyHours += $this->days[$i]->standbyHours;
        }

        $scaleFactor = ($totalOfServiceHours + $totalOfStandbyHours) / $totalQuotaOfHours;

        /*echo '$totalQuotaOfHours: ' . $totalQuotaOfHours . '<br />';
        echo '$totalOfServiceHours: ' . $totalOfServiceHours . '<br />';
        echo '$totalOfStandbyHours: ' . $totalOfStandbyHours . '<br />';
        echo '$scaleFactor: ' . $scaleFactor . '<hr />';*/

        if ($scaleFactor < 1) {
            $scaleFactor = 1;
        }

        // calculate score table
        $priorities = $this->team->getPriorities();
        $scoreTable = $this->assistanceInput->assistanceInput;
        $preferredWeekdays = $this->team->getPreferredWeekdays();
        $keyWords = $this->team->getKeyWords();

        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {

            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $scoreTable[$name][$i - 1] *= $priorities[$name];
                if ($preferredWeekdays[$name][$this->days[$i]->weekday - 1] == 1) {
                    $scoreTable[$name][$i - 1] *= 2;
                }
                foreach ($keyWords[$name] as $keyWord) {
                    if ($keyWord != "") {
                        if (strpos(strtolower($this->days[$i]->privateNotes), strtolower($keyWord)) !== false) {
                            $scoreTable[$name][$i - 1] *= 10;
                        }

                    }
                }
            }
        }

        // convert data
        $convertedData = array();
        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                if ($scoreTable[$name][$i - 1] == 0) {
                    continue;
                }
                if (substr($this->days[$i]->serviceBegin, 0, 2) == "--") {
                    continue;
                }
                $entry = array();
                array_push($entry, $scoreTable[$name][$i - 1]);
                array_push($entry, $name);
                array_push($entry, $i);

                array_push($convertedData, $entry);
            }
        }

        //$this->printConvertedDataTable($convertedData);

        $countOfRuns = 1000;
        $smallestDifference = PHP_INT_MAX;
        $servicePersonsBest = array();
        $standbyPersonsBest = array();

        $start = microtime(true);
        for ($run = 0; $run < $countOfRuns; $run++) {

            shuffle($convertedData); // shuffle, so not all services are in a row
            usort($convertedData, 'compare');

            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $this->servicePerson[$i] = "";
                $this->standbyPerson[$i] = "";
            }

            $quotaOfHours = $this->team->getHours($scaleFactor);

            // set service
            $serviceTolerance = 1;
            $serviceRun = 0;
            while (!$this->isServiceComplete()) {
                for ($i = 0; $i < count($convertedData); $i++) {
                    if ($this->servicePerson[$convertedData[$i][2]] == "") {
                        if ($quotaOfHours[$convertedData[$i][1]] - $this->days[$convertedData[$i][2]]->serviceHours >= 0 - ($serviceTolerance * $serviceRun)) {
                            $this->servicePerson[$convertedData[$i][2]] = $convertedData[$i][1];
                            $quotaOfHours[$convertedData[$i][1]] -= $this->days[$convertedData[$i][2]]->serviceHours;
                        }
                    }
                }
                $serviceRun++;
            }

            // set standby
            $standbyTolerance = 0.5;
            $standbyRun = 0;
            while (!$this->isStandbyComplete()) {
                for ($i = 0; $i < count($convertedData); $i++) {
                    if ($this->standbyPerson[$convertedData[$i][2]] == "" && $this->servicePerson[$convertedData[$i][2]] != $convertedData[$i][1]) {
                        if ($quotaOfHours[$convertedData[$i][1]] - $this->days[$convertedData[$i][2]]->standbyHours >= 0 - ($standbyTolerance * $standbyRun)) {
                            $this->standbyPerson[$convertedData[$i][2]] = $convertedData[$i][1];
                            $quotaOfHours[$convertedData[$i][1]] -= $this->days[$convertedData[$i][2]]->standbyHours;
                        }
                    }

                }
                $standbyRun++;
            }

            $currentDifference = $serviceRun * $serviceTolerance + $standbyRun * $standbyTolerance;
            if ($currentDifference < $smallestDifference) {
                $smallestDifference = $currentDifference;
                $servicePersonsBest = $this->servicePerson;
                $standbyPersonsBest = $this->standbyPerson;

                /*echo '$run: ' . $run . '<br />';
                echo '$currentDifference: ' . $currentDifference . '<br />';
                echo '$serviceRun: ' . $serviceRun . '<br />';
                echo '$standbyRun: ' . $standbyRun . '<hr />';*/

            }
        }

        /*$time_taken = (microtime(true) - $start) * 1000;
        echo 'time taken: ' . $time_taken . ' ms <br />';*/

        $this->servicePerson = $servicePersonsBest;
        $this->standbyPerson = $standbyPersonsBest;
    }

    private function createRosterAlgorithm6()
    {
        // take priority into account
        // take preferred weekdays into account
        // take keywords (in the month plan) into account
        // take working hours into account
        // generate score table and sort
        // generate a lot of rosters and take the best in the end

        if (!$this->assistanceInput->dataExist) {
            return;
        }

        // check that there is enough availability
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $sum = 0;
            foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
                if ($dates[$i - 1] > 0) {
                    $sum++;
                }
            }
            //echo 'day: ' . $i . ' sum: ' . $sum . '<br />';
            if ($sum < 2) {
                return;
            }
        }

        // calculate complete quota of hours
        $totalQuotaOfHours = 0;
        $quotaOfHours = $this->team->getHours();
        foreach ($quotaOfHours as $name => $value) {
            if (array_key_exists($name, $this->assistanceInput->assistanceInput)) {
                // only add value, if assistant has provided information for this month
                $totalQuotaOfHours += $value;
            }
        }

        // calculate service and standby hours
        $totalOfServiceHours = 0;
        $totalOfStandbyHours = 0;
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $totalOfServiceHours += $this->days[$i]->serviceHours;
            $totalOfStandbyHours += $this->days[$i]->standbyHours;
        }

        $scaleFactor = ($totalOfServiceHours + $totalOfStandbyHours) / $totalQuotaOfHours;

        /*echo '$totalQuotaOfHours: ' . $totalQuotaOfHours . '<br />';
        echo '$totalOfServiceHours: ' . $totalOfServiceHours . '<br />';
        echo '$totalOfStandbyHours: ' . $totalOfStandbyHours . '<br />';
        echo '$scaleFactor: ' . $scaleFactor . '<hr />';*/

        if ($scaleFactor < 1) {
            $scaleFactor = 1;
        }

        // calculate score table
        $priorities = $this->team->getPriorities();
        $scoreTable = $this->assistanceInput->assistanceInput;
        $preferredWeekdays = $this->team->getPreferredWeekdays();
        $keyWords = $this->team->getKeyWords();

        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {

            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $scoreTable[$name][$i - 1] *= $priorities[$name];
                if ($preferredWeekdays[$name][$this->days[$i]->weekday - 1] == 1) {
                    $scoreTable[$name][$i - 1] *= 2;
                }
                foreach ($keyWords[$name] as $keyWord) {
                    if ($keyWord != "") {
                        if (strpos(strtolower($this->days[$i]->privateNotes), strtolower($keyWord)) !== false) {
                            $scoreTable[$name][$i - 1] *= 10;
                        }

                    }
                }
            }
        }

        // convert data
        $convertedData = array();
        foreach ($this->assistanceInput->assistanceInput as $name => $dates) {
            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                if ($scoreTable[$name][$i - 1] == 0) {
                    continue;
                }
                $entry = array();
                array_push($entry, $scoreTable[$name][$i - 1]);
                array_push($entry, $name);
                array_push($entry, $i);

                array_push($convertedData, $entry);
            }
        }
        //usort($convertedData, 'compare');

        //$this->printConvertedDataTable($convertedData);

        $countOfRuns = 1;
        $smallestDifference = PHP_INT_MAX;
        $servicePersonsBest = array();
        $standbyPersonsBest = array();

        $start = microtime(true);
        for ($run = 0; $run < $countOfRuns; $run++) {
            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $this->servicePerson[$i] = "";
                $this->standbyPerson[$i] = "";
            }

            $quotaOfHours = $this->team->getHours($scaleFactor);

            // set service
            $serviceTolerance = 1;
            $serviceRun = 0;
            while (!$this->isServiceComplete()) {
                for ($i = 0; $i < count($convertedData); $i++) {
                    if ($this->servicePerson[$convertedData[$i][2]] == "") {
                        if ($quotaOfHours[$convertedData[$i][1]] - $this->days[$convertedData[$i][2]]->serviceHours >= 0 - ($serviceTolerance * $serviceRun)) {
                            $this->servicePerson[$convertedData[$i][2]] = $convertedData[$i][1];
                            $quotaOfHours[$convertedData[$i][1]] -= $this->days[$convertedData[$i][2]]->serviceHours;
                        }
                    }
                }
                $serviceRun++;
            }

            // set standby
            $standbyTolerance = 0.5;
            $standbyRun = 0;
            while (!$this->isStandbyComplete()) {
                for ($i = 0; $i < count($convertedData); $i++) {
                    if ($this->standbyPerson[$convertedData[$i][2]] == "" && $this->servicePerson[$convertedData[$i][2]] != $convertedData[$i][1]) {
                        if ($quotaOfHours[$convertedData[$i][1]] - $this->days[$convertedData[$i][2]]->standbyHours >= 0 - ($standbyTolerance * $standbyRun)) {
                            //echo "standby run: " . $standbyRun . "<br />";
                            $this->standbyPerson[$convertedData[$i][2]] = $convertedData[$i][1];
                            //echo "standby person: " . $convertedData[$i][1] . "<br />";
                            $quotaOfHours[$convertedData[$i][1]] -= $this->days[$convertedData[$i][2]]->standbyHours;
                            //echo "quota: " . $quotaOfHours[$convertedData[$i][1]] . "<br />";
                        }
                    }

                }
                $standbyRun++;
            }

            $currentDifference = $serviceRun * $serviceTolerance + $standbyRun * $standbyTolerance;
            if ($currentDifference < $smallestDifference) {
                $smallestDifference = $currentDifference;
                $servicePersonsBest = $this->servicePerson;
                $standbyPersonsBest = $this->standbyPerson;

                /*echo '$run: ' . $run . '<br />';
                echo '$currentDifference: ' . $currentDifference . '<br />';
                echo '$serviceRun: ' . $serviceRun . '<br />';
                echo '$standbyRun: ' . $standbyRun . '<hr />';*/

            }
        }

        /*$time_taken = (microtime(true) - $start) * 1000;
        echo 'time taken: ' . $time_taken . ' ms <br />';*/

        $this->servicePerson = $servicePersonsBest;
        $this->standbyPerson = $standbyPersonsBest;
    }

    private function printScoreTable($scoreTable)
    {
        echo '<h1>Score-Table</h1>';

        echo '<table>';

        echo '<tr>';
        echo '<th>Tag</th>';
        echo '<th>Bemerkungen (privat)</th>';
        foreach ($scoreTable as $name => $dates) {
            echo '<th>' . $name . '</th>';
        }
        echo '</tr>';

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            echo '<tr>';
            echo '<td>' . get_short_date($this->year, $this->month, $i) . '</td>';
            echo '<td>' . $this->days[$i]->privateNotes . '</td>';
            foreach ($scoreTable as $name => $dates) {
                echo '<td>' . $dates[$i - 1] . '</td>';
            }
            echo '</tr>';
        }

        echo '</table>';
    }

    private function printConvertedDataTable($convertedData)
    {
        echo '<h1>Score-Table sorted</h1>';
        echo '<table>';
        echo '<tr>';
        echo '<th>Rank</th>';
        echo '<th>Score</th>';
        echo '<th>Name</th>';
        echo '<th>Day</th>';
        echo '</tr>';
        for ($i = 0; $i < count($convertedData); $i++) {
            echo '<tr>';
            echo '<td>' . ($i + 1) . '</td>';
            echo '<td>' . $convertedData[$i][0] . '</td>';
            echo '<td>' . $convertedData[$i][1] . '</td>';
            echo '<td>' . $convertedData[$i][2] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    private function isRosterComplete()
    {
        return $this->isServiceComplete() && $this->isStandbyComplete();
    }

    private function isServiceComplete()
    {
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            if ($this->servicePerson[$i] == "" && substr($this->days[$i]->serviceBegin, 0, 2) != "--") {
                return false;
            }
        }
        return true;
    }

    private function isStandbyComplete()
    {
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            if ($this->standbyPerson[$i] == "" && substr($this->days[$i]->serviceBegin, 0, 2) != "--") {
                return false;
            }
        }
        return true;
    }

    public function getDays()
    {
        return $this->days;
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

    private function hasPublicNotes()
    {
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            if ($this->days[$i]->publicNotes != "") {
                return true;
            }
        }
        return false;
    }

    private function printTableHeaderMonthPlan()
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


        echo '<td>';

        echo '<select size="1">';

        foreach ($this->defaultWorkingTimes->startTimes as $startTime) {
            echo '<option';
            if ($startTime == $day->serviceBegin) {
                echo ' selected="selected"';
            }
            echo '>' . $startTime . '</option>';
        }

        echo '</select>';
        echo '<td>';

        echo '<select size="1">';

        foreach ($this->defaultWorkingTimes->endTimes as $endTime) {
            echo '<option';
            if ($endTime == $day->serviceEnd) {
                echo ' selected="selected"';
            }
            echo '>' . $endTime . '</option>';
        }

        echo '</select>';

        echo '<td><input onchange="validateString(this)" onblur="validateString(this)" value="' . htmlspecialchars($day->publicNotes) . '" type="text" size="30" maxlength="200" /></td>';
        echo '<td><input onchange="validateString(this)" onblur="validateString(this)" value="' . htmlspecialchars($day->privateNotes) . '" type="text" size="30" maxlength="200" /></td>';

        echo '</tr>';
    }

    public function printNotesInputForAdmin()
    {
        echo '<br />';
        echo '<h1>Nachricht an das Team</h1>';
        echo '<textarea onchange="validateString(this); save(' . $this->year . ', ' . $this->month . ')" onblur="validateString(this); save(' . $this->year . ', ' . $this->month . ')" id="notes" name="notes" cols="100" rows="10">' . implode('&#10;', $this->notes) . '</textarea>';
        echo '<br />';
    }

    public function printNotesInputForAssistant()
    {
        echo '<br />';
        echo '<h1>Nachricht an ' . $this->settings->adminName . '</h1>';
        echo '<textarea onchange="validateString(this)" onblur="validateString(this)" id="notesAssistant" name="notesAssistant" cols="100" rows="10">' . str_replace("<br />", "&#10;", $this->assistanceInput->assistanceNotes[$_SESSION['assistantName']]) . '</textarea>';
        echo '<br />';
    }

    public function printTable()
    {
        echo '<h1>Monatsplan für ' . get_month_description($this->month) . ' ' . $this->year . '</h1>';

        echo '<table>';
        $this->printTableHeaderMonthPlan();

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

        echo '<table id="' . $this->calendarId . '" >';

        $this->printCalendarHeader();

        echo "<tr>";

        $cellCounter = 0;

        // print empty cells
        for ($i = 1; $i < $weekday; $i++) {
            echo "<td></td>";
            $cellCounter++;
        }

        $dataStored = false;
        if (array_key_exists($_SESSION['assistantName'], $this->assistanceInput->assistanceInput)) {
            $dataStored = true;
        }

        // print days
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $className = "bad";
            if ($dataStored) {
                if ($this->assistanceInput->assistanceInput[$_SESSION['assistantName']][$i - 1] == 1) {
                    $className = "okay";
                }
                if ($this->assistanceInput->assistanceInput[$_SESSION['assistantName']][$i - 1] == 10) {
                    $className = "good";
                }
            }
            echo '<td class="' . $className . '" onclick="dateClicked(this)"><b>' . $i . '</b><br />';
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
            echo '<h2>Bemerkungen zu den Terminen</h2>';
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

    public function printNotesFromAdmin()
    {
        $notes = implode("<br />", $this->notes);

        if ($notes != "") {
            echo '<h2>Allgemeine Bemerkungen von ' . $this->settings->adminName . '</h2>';
            echo '<div class="wrapLongText">';
            echo $notes;
            echo '</div>';

        }
    }
}

?>