<?php

require_once 'TeamOrganisationInterface.php';

require_once 'TeamMember.php';
require_once 'Passwords.php';
require_once 'functions.php';


class Team implements TeamOrganisationInterface
{
    private $tableId = "teamTable";
    private $fileName;

    public $teamMembers = array();
    public $numberOfTeamMembers = 0; //TODO remove, since this information is contained in $teamMembers-array

    function __construct()
    {
        $this->fileName = "../Data/" . strtolower($_SESSION['clientName']) . "/Team/team.txt";
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

    public function saveMember($oldId, $teamMember)
    {
        if ($oldId == "") { // add team member
            array_push($this->teamMembers, $teamMember);
            $this->numberOfTeamMembers++;
        } else { // update member
            for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
                if ($this->teamMembers[$i]->loginName == $oldId) {
                    $this->teamMembers[$i] = $teamMember;
                    break;
                }
            }
        }
        $this->saveTeamToFile();
    }

    public function removeMember($id)
    {
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            if ($this->teamMembers[$i]->loginName == $id) {
                unset($this->teamMembers[$i]);
                $this->teamMembers = array_values($this->teamMembers);
                $this->numberOfTeamMembers--;
                $this->saveTeamToFile();
                break;
            }
        }
    }

    public function saveTeamToFile()
    {
        $filePath = substr($this->fileName, 0, strrpos($this->fileName, '/'));

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }

        $fh = fopen($this->fileName, "w");

        fwrite($fh, $this->numberOfTeamMembers . "\n");

        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            fwrite($fh, $this->teamMembers[$i]->loginName . "\n");
            fwrite($fh, $this->teamMembers[$i]->firstName . "\n");
            fwrite($fh, $this->teamMembers[$i]->lastName . "\n");
            fwrite($fh, $this->teamMembers[$i]->eMailAddress . "\n");
            fwrite($fh, $this->teamMembers[$i]->phoneNumber . "\n");
            fwrite($fh, implode(";", $this->teamMembers[$i]->keyWords) . "\n");
            fwrite($fh, $this->teamMembers[$i]->hoursPerMonth . "\n");
            fwrite($fh, $this->teamMembers[$i]->priority . "\n");
            fwrite($fh, implode(";", $this->teamMembers[$i]->preferredWeekdays) . "\n");
        }


        fclose($fh);

        $passwords = new Passwords($_SESSION['clientName']);
        $passwords->checkTeam($this->getLoginNames());
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

    public function printTeamMemberBusinessCard($teamMember)
    {
        echo '<div class="businessCard" id="' . $teamMember->loginName . '">';
        echo '<h1>' . $teamMember->loginName . '</h1>';
        echo '<table>';
        echo '<tbody>';
        echo '<tr>';
        echo '<td>ID</td>';
        echo '<td>' . $teamMember->loginName . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Vorname</td>';
        echo '<td>' . $teamMember->firstName . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Nachname</td>';
        echo '<td>' . $teamMember->lastName . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>E-Mail Adresse</td> ';
        echo '<td>' . $teamMember->eMailAddress . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Telefonnummer</td>';
        echo '<td>' . $teamMember->phoneNumber . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Stichwörter</td>';
        echo '<td>' . implode(" ", $teamMember->keyWords) . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Stundenkontigent</td>';
        echo '<td>' . $teamMember->hoursPerMonth . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Priorisierung</td>';
        echo '<td>' . $teamMember->priority . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Bevorzugte Tage</td>';

        echo '<td>';
        $weekdays = get_weekdays();
        $firstPrinted = false;
        for ($j = 0; $j < 7; $j++) {
            if ($teamMember->preferredWeekdays[$j] == 1) {
                if ($firstPrinted) {
                    echo ' + ';
                }
                else {
                    $firstPrinted = true;
                }
                echo $weekdays[$j];
            }
        }
        echo '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Aktionen</td>';
        echo '<td>';
        echo '<input type="button" onclick="editMember(this)" value="Editieren">';
        echo '<input type="button" onclick="resetPassword(this)" value="Passwort zurücksetzen">';
        echo '<input type="button" onclick="removeMember(this)" value="Löschen">';
        echo '</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

    public function printAllTeamMembers()
    {
        echo '<div id="team">';
        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $this->printTeamMemberBusinessCard($this->teamMembers[$i]);
        }
        echo '</div>';
    }


    public function printTable()
    {
        echo '<h1 > Team Übersicht </h1 > ';

        echo '<table id = "' . $this->tableId . '" > ';
        $this->printHeader();

        if ($this->numberOfTeamMembers == 0) {
            echo '</table > ';
            return;
        }

        for ($i = 0; $i < $this->numberOfTeamMembers; $i++) {
            $this->printTeamMember($this->teamMembers[$i]);
        }

        echo '</table > ';
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