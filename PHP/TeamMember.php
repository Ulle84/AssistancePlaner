<?php

require 'functions.php';

class TeamMember
{
    public $firstName;
    public $lastName;
    public $eMailAddress;
    public $phoneNumber;
    public $hoursPerMonth;
    public $priority;
    public $preferredWeekdays = array();
    private $tableHeaders = array("Vorname", "Nachname", "E-Mail Adresse", "Telefonnummer", "Stundenkontingent", "Priorisierung", "Bevorzugte Tage", "Löschen");

    public function printHeader()
    {
        echo '<tr>';

        foreach ($this->tableHeaders as $value) {
            echo '<th>' . $value . '</th>';
        }

        echo '</tr>';
    }

    public function printContent()
    {
        echo '<tr>';

        echo '<td class="left" onclick="edit(this)">' . $this->firstName . '</td>';
        echo '<td class="left" onclick="edit(this)">' . $this->lastName . '</td>';
        echo '<td class="left" onclick="edit(this)">' . $this->eMailAddress . '</td>';
        echo '<td class="left" onclick="edit(this)">' . $this->phoneNumber . '</td>';
        echo '<td class="left" onclick="edit(this)">' . $this->hoursPerMonth . '</td>';
        echo '<td class="left" onclick="edit(this)">' . $this->priority . '</td>';

        $weekdays = get_weekdays();
        echo '<td>';
        for ($j = 0; $j < 7; $j++) {
            echo '<input type="checkbox" value="' . $weekdays[$j] . '"';
            if (in_array($weekdays[$j], $this->preferredWeekdays)) {
                echo ' checked="true"';
            }

            echo '/><span>' . $weekdays[$j] . ' </span>';
        }
        echo '</td>';

        echo '<td class="left"><input type="button" value="Löschen" onclick="removeMember(this)" /></div></td>';

        echo '</tr>';
    }
}

?>