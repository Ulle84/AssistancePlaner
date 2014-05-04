<?php

require_once 'TeamMember.php';
require_once 'functions.php';

class Team
{
    private $tableId = "teamTable";

    public $teamMembers = array();
    public $numberOfTeamMembers = 0;

    function __construct()
    {
        $this->readFromFile("../Data/Team/team.txt");
    }

    public function setTableId($tableId)
    {
        $this->tableId = $tableId;
    }

    public function readFromFile($fileName)
    {
        $this->numberOfTeamMembers = 0;
        $this->teamMembers = array();

        if (file_exists($fileName)) {
            $file = fopen($fileName, "r");

            $this->numberOfTeamMembers = (int)rtrim(fgets($file));


            for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
                $teamMember = new TeamMember();

                $teamMember->firstName = rtrim(fgets($file));
                $teamMember->lastName = rtrim(fgets($file));
                $teamMember->eMailAddress = rtrim(fgets($file));
                $teamMember->phoneNumber = rtrim(fgets($file));
                $teamMember->hoursPerMonth = (int)rtrim(fgets($file));
                $teamMember->priority = (int)rtrim(fgets($file));
                $teamMember->preferredWeekdays = explode(";", rtrim(fgets($file)));

                $this->teamMembers[$i] = $teamMember;
            }

            fclose($file);
        }
    }

    private function printHeader()
    {
        echo '<tr>';

        echo '<th>Vorname</th>';
        echo '<th>Nachname</th>';
        echo '<th>E-Mail Adresse</th>';
        echo '<th>Telefonnummer</th>';
        echo '<th>Stundenkontingent</th>';
        echo '<th>Priorisierung</th>';
        echo '<th>Bevorzugte Tage</th>';
        echo '<th>Löschen</th>';

        echo '</tr>';
    }

    private function printTeamMember($teamMember)
    {
        echo '<tr>';

        echo '<td class="left" onclick="edit(this)">' . $teamMember->firstName . '</td>';
        echo '<td class="left" onclick="edit(this)">' . $teamMember->lastName . '</td>';
        echo '<td class="left" onclick="edit(this)">' . $teamMember->eMailAddress . '</td>';
        echo '<td class="left" onclick="edit(this)">' . $teamMember->phoneNumber . '</td>';
        echo '<td class="left" onclick="edit(this)">' . $teamMember->hoursPerMonth . '</td>';
        echo '<td class="left" onclick="edit(this)">' . $teamMember->priority . '</td>';


        /*echo '<td><input type="text" size="12" maxlength="50" value="' . $teamMember->firstName . '"/></td>';
        echo '<td><input type="text" size="18" maxlength="50" value="' . $teamMember->lastName . '"/></td>';
        echo '<td><input type="text" size="15" maxlength="50" value="' . $teamMember->eMailAddress . '"/></td>';
        echo '<td><input type="text" size="15" maxlength="50" value="' . $teamMember->phoneNumber . '"/></td>';
        echo '<td><input type="text" size="18" maxlength="3" style="text-align: right" value="' . $teamMember->hoursPerMonth . '"/></td>';
        echo '<td><input type="text" size="11" maxlength="2" style="text-align: right" value="' . $teamMember->priority . '"/></td>';*/

        $weekdays = get_weekdays();
        echo '<td>';
        for ($j = 0; $j < 7; $j++) {
            echo '<input type="checkbox" value="' . $weekdays[$j] . '"';

            if (in_array($weekdays[$j], $teamMember->preferredWeekdays)) {
                echo ' checked="true"';
            }

            echo '/><span>' . $weekdays[$j] . ' </span>';
        }
        echo '</td>';

        echo '<td class="left"><input type="button" value="Löschen" onclick="removeMember(this)" /></div></td>';

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

    public function getHours()
    {
        //TODO use real key, not the firstName
        $hours = array();
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $hours[$this->teamMembers[$i]->firstName] = $this->teamMembers[$i]->hoursPerMonth;
        }
        return $hours;
    }

    public function getPriorities()
    {
        //TODO use real key, not the firstName
        $priorities = array();
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $priorities[$this->teamMembers[$i]->firstName] = $this->teamMembers[$i]->priority;
        }
        return $priorities;
    }

    public function getPreferredWeekdays()
    {
        //TODO use real key, not the firstName
        $weekdays = array();
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $weekdays[$this->teamMembers[$i]->firstName] = $this->teamMembers[$i]->preferredWeekdays;
        }
        return $weekdays;
    }
}

?>