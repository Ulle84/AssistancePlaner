<?php

require_once 'TeamMember.php';
require_once 'functions.php';
require_once 'Settings.php'; //TODO should be removed

class Team
{
    private $tableId = "teamTable";
    private $fileName;

    public $teamMembers = array();
    public $numberOfTeamMembers = 0;

    function __construct()
    {
        $this->fileName = "../Data/" . $_SESSION['clientName'] . "/Team/team.txt";
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

    public function getMailAddressFromId($id)
    {
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            if ($this->teamMembers[$i]->loginName == $id) {
                return $this->teamMembers[$i]->eMailAddress;
            }
        }

        return "";
    }

    public function getFullNameFromId($id)
    {
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            if ($this->teamMembers[$i]->loginName == $id) {
                $completeName = $this->teamMembers[$i]->firstName . ' ' . $this->teamMembers[$i]->lastName;
                return $completeName;
            }
        }
        return "";
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
                $teamMember->keyWords = explode(" ", rtrim(fgets($file)));
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
        $filePath = substr($this->fileName, 0, strrpos($this->fileName, '/'));

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }

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

        echo '<th>Kennung</th>';
        echo '<th>Vorname</th>';
        echo '<th>Nachname</th>';
        echo '<th>E-Mail Adresse</th>';
        echo '<th>Telefonnummer</th>';
        echo '<th>Stichwörter (getrennt durch Leerzeichen)</th>';
        echo '<th>Stundenkontingent</th>';
        echo '<th>Priorisierung</th>';
        echo '<th>Bevorzugte Wochentage</th>';
        echo '<th>Aktionen</th>';

        echo '</tr>';
    }

    private function printTeamMember($teamMember)
    {
        echo '<tr>';

        echo '<td><input onchange="validateString(this)" onblur="validateString(this)" type="text" size="12" maxlength="50" value="' . $teamMember->loginName . '"/></td>';
        echo '<td><input onchange="validateString(this)" onblur="validateString(this)" type="text" size="12" maxlength="50" value="' . $teamMember->firstName . '"/></td>';
        echo '<td><input onchange="validateString(this)" onblur="validateString(this)" type="text" size="18" maxlength="50" value="' . $teamMember->lastName . '"/></td>';
        echo '<td><input onchange="validateString(this)" onblur="validateString(this)" type="text" size="15" maxlength="50" value="' . $teamMember->eMailAddress . '"/></td>';
        echo '<td><input onchange="validateString(this)" onblur="validateString(this)" type="text" size="15" maxlength="50" value="' . $teamMember->phoneNumber . '"/></td>';
        echo '<td><input onchange="validateString(this)" onblur="validateString(this)" type="text" size="18" maxlength="50" value="' . implode(" ", $teamMember->keyWords) . '"/></td>';
        echo '<td><input onchange="validateInteger(this, 0, 999)" onblur="validateInteger(this, 0, 999)" type="text" size="18" maxlength="3" style="text-align: right" value="' . $teamMember->hoursPerMonth . '"/></td>';
        echo '<td><input onchange="validateInteger(this, 1, 999)" onblur="validateInteger(this, 1, 999)" type="text" size="11" maxlength="3" style="text-align: right" value="' . $teamMember->priority . '"/></td>';

        $weekdays = get_weekdays();
        echo '<td style="min-width: 300px">';
        for ($j = 0; $j < 7; $j++) {
            echo '<span><input type="checkbox" value="' . $weekdays[$j] . '"';

            if ($teamMember->preferredWeekdays[$j] == 1) {
                echo ' checked="true"';
            }

            echo '/>' . $weekdays[$j] . '&nbsp;</span>';
        }
        echo '</td>';

        echo '<td class="left" style="min-width: 250px"><input type="button" value="Löschen" onclick="removeMember(this)" />';
        echo '<input type="button" value="Passwort zurücksetzen" onclick="resetPassword(this)" /></td>';

        echo '</tr>';
    }

    public function printTable()
    {
        echo '<h1>Team Übersicht</h1>';

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