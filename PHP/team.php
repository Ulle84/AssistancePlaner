<?php

require_once 'TeamMember.php';
require_once 'functions.php';
require_once 'Settings.php';

class Team
{
    private $tableId = "teamTable";
    private $fileName = "../Data/Team/team.txt";

    public $teamMembers = array();
    public $numberOfTeamMembers = 0;

    function __construct()
    {
        $this->readFromFile();
    }

    public function setTableId($tableId)
    {
        $this->tableId = $tableId;
    }

    public function getMailAddresses()
    {
        $mailAddresses = array();
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $completeName = $this->teamMembers[$i]->firstName . ' ' . $this->teamMembers[$i]->lastName;
            if ($this->teamMembers[$i]->eMailAddress != "") {
                $mailAddresses[$this->teamMembers[$i]->eMailAddress] = $completeName;
            }
        }
        return $mailAddresses;
    }

    public function readFromFile()
    {
        $this->numberOfTeamMembers = 0;
        $this->teamMembers = array();

        if (file_exists($this->fileName)) {
            $file = fopen($this->fileName, "r");

            $this->numberOfTeamMembers = (int)rtrim(fgets($file));


            for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
                $teamMember = new TeamMember();

                $teamMember->loginName = rtrim(fgets($file));
                $teamMember->firstName = rtrim(fgets($file));
                $teamMember->lastName = rtrim(fgets($file));
                $teamMember->eMailAddress = rtrim(fgets($file));
                $teamMember->phoneNumber = rtrim(fgets($file));
                $teamMember->keyWords = explode("; ", rtrim(fgets($file)));
                $teamMember->hoursPerMonth = (int)rtrim(fgets($file));
                $teamMember->priority = (int)rtrim(fgets($file));
                $teamMember->preferredWeekdays = explode(";", rtrim(fgets($file)));

                $this->teamMembers[$i] = $teamMember;
            }

            fclose($file);
        }
    }

    public function saveToFile($content)
    {
        $fh = fopen($this->fileName, "w");
        fwrite($fh, ($content));
        fclose($fh);

        $this->readFromFile();
    }

    public function getLoginNames()
    {
        $loginNames = array();
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            array_push($loginNames, $this->teamMembers[$i]->loginName);
        }
        return $loginNames;
    }

    private function printHeader()
    {
        echo '<tr>';

        echo '<th>Login Name</th>';
        echo '<th>Vorname</th>';
        echo '<th>Nachname</th>';
        echo '<th>E-Mail Adresse</th>';
        echo '<th>Telefonnummer</th>';
        echo '<th>Stichwörter</th>';
        echo '<th>Stundenkontingent</th>';
        echo '<th>Priorisierung</th>';
        echo '<th>Bevorzugte Tage</th>';
        echo '<th>Aktionen</th>';

        echo '</tr>';
    }

    private function printTeamMember($teamMember)
    {
        echo '<tr>';

        echo '<td><input onchange="validate(this, 0)" onblur="validate(this, 0)" type="text" size="12" maxlength="50" value="' . $teamMember->loginName . '"/></td>';
        echo '<td><input onchange="validate(this, 0)" onblur="validate(this, 0)" type="text" size="12" maxlength="50" value="' . $teamMember->firstName . '"/></td>';
        echo '<td><input onchange="validate(this, 0)" onblur="validate(this, 0)" type="text" size="18" maxlength="50" value="' . $teamMember->lastName . '"/></td>';
        echo '<td><input onchange="validate(this, 0)" onblur="validate(this, 0)" type="text" size="15" maxlength="50" value="' . $teamMember->eMailAddress . '"/></td>';
        echo '<td><input onchange="validate(this, 0)" onblur="validate(this, 0)" type="text" size="15" maxlength="50" value="' . $teamMember->phoneNumber . '"/></td>';
        echo '<td><input onchange="validate(this, 0)" onblur="validate(this, 0)" type="text" size="18" maxlength="50" value="' . implode("; ", $teamMember->keyWords) . '"/></td>';
        echo '<td><input onchange="validate(this, 1)" onblur="validate(this, 0)" type="text" size="18" maxlength="3" style="text-align: right" value="' . $teamMember->hoursPerMonth . '"/></td>';
        echo '<td><input onchange="validate(this, 1)" onblur="validate(this, 0)" type="text" size="11" maxlength="3" style="text-align: right" value="' . $teamMember->priority . '"/></td>';

        $weekdays = get_weekdays();
        echo '<td>';
        for ($j = 0; $j < 7; $j++) {
            echo '<input type="checkbox" value="' . $weekdays[$j] . '"';

            if ($teamMember->preferredWeekdays[$j] == 1) {
                echo ' checked="true"';
            }

            echo '/><span>' . $weekdays[$j] . ' </span>';
        }
        echo '</td>';

        echo '<td class="left"><input type="button" value="Löschen" onclick="removeMember(this)" />';
        echo '<input type="button" value="Passwort zurücksetzen" onclick="resetPassword(this)" /></td>';

        echo '</tr>';
    }

    public function printTable()
    {
        echo '<h1>Team Übersicht</h1>';

        $settings = new Settings();
        echo '<div class="forbiddenName">developer</div>';
        echo '<div class="forbiddenName">' . $settings->adminName . '</div>';

        echo '<table id="' . $this->tableId . '">';
        $this->printHeader();

        if ($this->numberOfTeamMembers == 0) {
            echo '</table>';
            return;
        }

        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $this->printTeamMember($this->teamMembers[$i]);
        }

        echo '</table>';
    }

    public function getHours($scaleFactor = 1)
    {
        $hours = array();
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $hours[$this->teamMembers[$i]->loginName] = $this->teamMembers[$i]->hoursPerMonth * $scaleFactor;
        }
        return $hours;
    }

    public function getPriorities()
    {
        $priorities = array();
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $priorities[$this->teamMembers[$i]->loginName] = $this->teamMembers[$i]->priority;
        }
        return $priorities;
    }

    public function getPreferredWeekdays()
    {
        $weekdays = array();
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $weekdays[$this->teamMembers[$i]->loginName] = $this->teamMembers[$i]->preferredWeekdays;
        }
        return $weekdays;
    }

    public function getKeyWords()
    {
        $keyWords = array();
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $keyWords[$this->teamMembers[$i]->loginName] = $this->teamMembers[$i]->keyWords;
        }
        return $keyWords;
    }
}

?>