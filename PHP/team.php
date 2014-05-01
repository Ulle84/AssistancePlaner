<?php

require 'TeamMember.php';

class Team
{
    private $tableId = "teamTable";

    public $teamMembers = array();
    public $numberOfTeamMembers = 0;

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

    public function printTable()
    {
        if ($this->numberOfTeamMembers == 0) {
            return;
        }

        echo '<table id="' . $this->tableId . '">';
        $this->teamMembers[0]->printHeader();
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $this->teamMembers[$i]->printContent();
        }
        echo '</table>';
    }
}

?>