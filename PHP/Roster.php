<?php

require_once 'AssistanceInput.php';
require_once 'Team.php';
require_once 'MonthPlan.php';

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

        $this->readFromFile("../Data/" . $_SESSION['clientName'] . "/Roster/" . $year . "-" . $month . ".txt");

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
        $fileName = "../Data/" . $_SESSION['clientName'] . "/Roster/" . $this->year . "-" . $this->month . ".txt";
        $fh = fopen($fileName, "w");
        fwrite($fh, date("d.m.Y H:i\n"));

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            fwrite($fh, $this->servicePerson[$i] . ';' . $this->standbyPerson[$i] . "\n");
        }

        fclose($fh);
    }

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
        //substr($this->serviceBegin, 3, 2)
        //$hourOfServiceBeg$this->monthPlan->days[$day]->serviceBegin

        $serviceHours = "10:00 - 11:00";

        if ($day != 1) {
            if ($this->standbyPerson[$day] == $this->servicePerson[$day - 1]) {
                $latterTime = $this->getLatterTime("10:00", $this->monthPlan->days[$day - 1]->serviceEnd);
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

        if ($this->monthPlan->days[$day]->serviceHours > 13) {
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

            echo '<td onclick="entryClicked(this)" class="' . $className . '" baseClass="' . $baseClassName . '">' . $cellTextContent . '</td>';
        }

        echo '<td class="left">' . $day->publicNotes . '</td>';
        echo '<td class="left">' . $day->privateNotes . '</td>';

        echo '</tr>';

    }

    private function printTableBase()
    {
        echo '<h1>Dienstplan für ' . get_month_description($this->month) . ' ' . $this->year . '</h1>';
        echo '<div>Letze Änderung: <span id="lastChange">' . $this->lastChange . '</span></div><br />';
        echo '<table id="rosterTable">';
    }

    public function printTableClient()
    {
        $this->printTableBase();

        $this->printTableHeader(true);

        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $this->printDayRow($this->monthPlan->days[$i]);
            if ($this->monthPlan->days[$i]->weekday == 7 && $i < $this->daysPerMonth) {
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
        if ($this->lastChange == "") {
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
            echo '<td class="left">' . $this->monthPlan->days[$i]->getWorkingHours() . '</td>';
            echo '<td class="left">' . $this->servicePerson[$i] . '</td>';

            echo '<td class="left">' . $this->getServiceHours($i) . '</td>';
            echo '<td class="left">' . $this->standbyPerson[$i] . '</td>';
            echo '<td class="left">' . $this->monthPlan->days[$i]->publicNotes . '</td>';
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
                echo '<td class="left">' . $this->monthPlan->days[$i]->getWorkingHours() . '</td>';
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
            $pdf->Cell($w[1], $cellHeight, $this->monthPlan->days[$i]->getWorkingHours(), 1, 0, 'C', false);
            $pdf->Cell($w[2], $cellHeight, utf8_decode($this->team->getFullNameFromId($this->servicePerson[$i])), 1, 0, 'L', false);

            $pdf->Cell($w[3], $cellHeight, $this->getServiceHours($i), 1, 0, 'L', false);
            $pdf->Cell($w[4], $cellHeight, utf8_decode($this->team->getFullNameFromId($this->standbyPerson[$i])), 1, 0, 'L', false);
        }

        $pdf->Output();
    }

    private function createRoster()
    {
        $this->createRosterAlgorithm6();
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

        // calculate service and standby hours
        $totalOfServiceHours = 0;
        $totalOfStandbyHours = 0;
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $totalOfServiceHours += $this->monthPlan->days[$i]->serviceHours;
            $totalOfStandbyHours += $this->monthPlan->days[$i]->standbyHours;
        }

        $scaleFactor = $totalOfServiceHours / ($totalOfServiceHours + $totalOfStandbyHours);

        if ($scaleFactor < 1) {
            $scaleFactor = 1;
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

        //$this->printScoreTable($scoreTable);

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

        $this->printConvertedDataTable($convertedData);


        $serviceRunMax = 2;
        $standbyRunMax = 5;

        $serviceRun = $serviceRunMax + 1;
        $standbyRun = $standbyRunMax + 1;

        $completeRun = 0;

        $start = microtime(true);
        while ($serviceRun > $serviceRunMax || $standbyRun > $standbyRunMax) {

            shuffle($convertedData); // shuffle, so not all services are in a row
            usort($convertedData, 'compare');

            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $this->servicePerson[$i] = "";
                $this->standbyPerson[$i] = "";
            }

            $quotaOfHours = $this->team->getHours($scaleFactor);

            // set service
            $serviceTolerance = 2;
            $serviceRun = 0;
            while (!$this->isServiceComplete()) {
                for ($i = 0; $i < count($convertedData); $i++) {
                    /*$maxServicesOnPiece = 2;
                    if ($this->daysPerMonth - $convertedData[$i][2] > $maxServicesOnPiece) {
                        if ($this->servicePerson[$convertedData[$i][2] + 1] == $convertedData[$i][1]) {
                            if ($this->servicePerson[$convertedData[$i][2] + 2] == $convertedData[$i][1]) {
                                continue;
                            }
                        }
                    }*/

                    if ($this->servicePerson[$convertedData[$i][2]] == "") {
                        if ($quotaOfHours[$convertedData[$i][1]] - $this->monthPlan->days[$convertedData[$i][2]]->serviceHours > 0 - ($serviceTolerance * $serviceRun)) {
                            $this->servicePerson[$convertedData[$i][2]] = $convertedData[$i][1];
                            $quotaOfHours[$convertedData[$i][1]] -= $this->monthPlan->days[$convertedData[$i][2]]->serviceHours;
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
                        if ($quotaOfHours[$convertedData[$i][1]] - $this->monthPlan->days[$convertedData[$i][2]]->standbyHours > 0 - ($standbyTolerance * $standbyRun)) {
                            $this->standbyPerson[$convertedData[$i][2]] = $convertedData[$i][1];
                            $quotaOfHours[$convertedData[$i][1]] -= $this->monthPlan->days[$convertedData[$i][2]]->standbyHours;
                        }
                    }

                }
                $standbyRun++;
            }

            /*echo "service-run-counter: " . $serviceRun . '<br />';
            echo "standby-run-counter: " . $standbyRun . '<br />';*/
            $completeRun++;
        }
        $time_taken = microtime(true) - $start;

        //$this->writeToFile();
    }

    private function createRosterAlgorithm5()
    {
        // strategy give the best rated the service and the second best rated the standby
        // look also for preferred weekdays
        // look also for the quota of hours
        // look also for the keywords in the month-plan

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
            $totalQuotaOfHours += $value;
        }

        // calculate service and standby hours
        $totalOfServiceHours = 0;
        $totalOfStandbyHours = 0;
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $totalOfServiceHours += $this->monthPlan->days[$i]->serviceHours;
            $totalOfStandbyHours += $this->monthPlan->days[$i]->standbyHours;
        }

        $scaleFactor = $totalQuotaOfHours / ($totalOfServiceHours + $totalOfStandbyHours);

        /*echo '$totalQuotaOfHours: ' . $totalQuotaOfHours . '<br />';
        echo '$totalOfServiceHours: ' . $totalOfServiceHours . '<br />';
        echo '$totalOfStandbyHours: ' . $totalOfStandbyHours . '<br />';
        echo '$scaleFactor: ' . $scaleFactor . '<br />';*/

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
                if ($preferredWeekdays[$name][$this->monthPlan->days[$i]->weekday - 1] == 1) {
                    $scoreTable[$name][$i - 1] *= 2;
                }
                foreach ($keyWords[$name] as $keyWord) {
                    if ($keyWord != "") {
                        if (strpos(strtolower($this->monthPlan->days[$i]->privateNotes), strtolower($keyWord)) !== false) {
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

        //$this->printConvertedDataTable($convertedData);


        // ToDo Werte weiter testen
        $serviceRunMax = 7; //2
        $standbyRunMax = 10; //4

        $serviceRun = $serviceRunMax + 1;
        $standbyRun = $standbyRunMax + 1;

        $completeRun = 0;

        $start = microtime(true);
        while ($serviceRun > $serviceRunMax || $standbyRun > $standbyRunMax) {

            shuffle($convertedData); // shuffle, so not all services are in a row
            usort($convertedData, 'compare');

            for ($i = 1; $i <= $this->daysPerMonth; $i++) {
                $this->servicePerson[$i] = "";
                $this->standbyPerson[$i] = "";
            }

            $quotaOfHours = $this->team->getHours($scaleFactor);

            // set service
            $serviceTolerance = 2;
            $serviceRun = 0;
            while (!$this->isServiceComplete()) {
                for ($i = 0; $i < count($convertedData); $i++) {
                    /*$maxServicesOnPiece = 2;
                    if ($this->daysPerMonth - $convertedData[$i][2] > $maxServicesOnPiece) {
                        if ($this->servicePerson[$convertedData[$i][2] + 1] == $convertedData[$i][1]) {
                            if ($this->servicePerson[$convertedData[$i][2] + 2] == $convertedData[$i][1]) {
                                continue;
                            }
                        }
                    }*/

                    if ($this->servicePerson[$convertedData[$i][2]] == "") {
                        if ($quotaOfHours[$convertedData[$i][1]] - $this->monthPlan->days[$convertedData[$i][2]]->serviceHours > 0 - ($serviceTolerance * $serviceRun)) {
                            $this->servicePerson[$convertedData[$i][2]] = $convertedData[$i][1];
                            $quotaOfHours[$convertedData[$i][1]] -= $this->monthPlan->days[$convertedData[$i][2]]->serviceHours;
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
                        if ($quotaOfHours[$convertedData[$i][1]] - $this->monthPlan->days[$convertedData[$i][2]]->standbyHours > 0 - ($standbyTolerance * $standbyRun)) {
                            $this->standbyPerson[$convertedData[$i][2]] = $convertedData[$i][1];
                            $quotaOfHours[$convertedData[$i][1]] -= $this->monthPlan->days[$convertedData[$i][2]]->standbyHours;
                        }
                    }

                }
                $standbyRun++;
            }
            $completeRun++;
        }
        $time_taken = (microtime(true) - $start) * 1000;

        //$this->writeToFile();
    }

    private function createRosterAlgorithm6()
    {
        // strategy give the best rated the service and the second best rated the standby
        // look also for preferred weekdays
        // look also for the quota of hours
        // look also for the keywords in the month-plan
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
            $totalQuotaOfHours += $value;
        }

        // calculate service and standby hours
        $totalOfServiceHours = 0;
        $totalOfStandbyHours = 0;
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            $totalOfServiceHours += $this->monthPlan->days[$i]->serviceHours;
            $totalOfStandbyHours += $this->monthPlan->days[$i]->standbyHours;
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
                if ($preferredWeekdays[$name][$this->monthPlan->days[$i]->weekday - 1] == 1) {
                    $scoreTable[$name][$i - 1] *= 2;
                }
                foreach ($keyWords[$name] as $keyWord) {
                    if ($keyWord != "") {
                        if (strpos(strtolower($this->monthPlan->days[$i]->privateNotes), strtolower($keyWord)) !== false) {
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

        $this->printConvertedDataTable($convertedData);

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
                        if ($quotaOfHours[$convertedData[$i][1]] - $this->monthPlan->days[$convertedData[$i][2]]->serviceHours > 0 - ($serviceTolerance * $serviceRun)) {
                            $this->servicePerson[$convertedData[$i][2]] = $convertedData[$i][1];
                            $quotaOfHours[$convertedData[$i][1]] -= $this->monthPlan->days[$convertedData[$i][2]]->serviceHours;
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
                        if ($quotaOfHours[$convertedData[$i][1]] - $this->monthPlan->days[$convertedData[$i][2]]->standbyHours > 0 - ($standbyTolerance * $standbyRun)) {
                            $this->standbyPerson[$convertedData[$i][2]] = $convertedData[$i][1];
                            $quotaOfHours[$convertedData[$i][1]] -= $this->monthPlan->days[$convertedData[$i][2]]->standbyHours;
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
            echo '<td>' . $this->monthPlan->days[$i]->privateNotes . '</td>';
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
            if ($this->servicePerson[$i] == "") {
                return false;
            }
        }
        return true;
    }

    private function isStandbyComplete()
    {
        for ($i = 1; $i <= $this->daysPerMonth; $i++) {
            if ($this->standbyPerson[$i] == "") {
                return false;
            }
        }
        return true;
    }
}

?>