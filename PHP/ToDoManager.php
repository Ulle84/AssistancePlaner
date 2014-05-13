<?php

require_once('ToDoItem.php');

function compare($value1, $value2)
{
    $a = $value1->dueDate;
    $b = $value2->dueDate;

    if ($a == $b) {
        if ($value1->description == $value2->description) {
            return 0;
        }
        return ($value1->description < $value2->description) ? -1 : +1;
    }

    if ($a == "") {
        return +1;
    }

    if ($b == "") {
        return -1;
    }

    return ($a < $b) ? -1 : +1;
}

class ToDoManager
{
    private $fileName;
    private $dataExist;
    private $toDos = array();

    function __construct()
    {
        $this->fileName = "../Data/ToDoManager/toDos.txt";
        $this->readFromFile();
    }

    private function readFromFile()
    {
        if (file_exists($this->fileName)) {
            $this->toDos = array();

            $file = fopen($this->fileName, "r");

            while (!feof($file)) {
                $description = rtrim(fgets($file));
                $dueDate = rtrim(fgets($file));

                if ($description != "") {
                    $toDoItem = new ToDoItem();
                    $toDoItem->description = $description;
                    $toDoItem->dueDate = $dueDate;
                    $toDoItem->dueDateDisplay = substr($dueDate, 8, 2) . '.' . substr($dueDate, 5, 2) . '.' . substr($dueDate, 0, 4);
                    array_push($this->toDos, $toDoItem);

                    $this->dataExist = true;
                }
            }
            fclose($file);
        }
    }

    private function printToDo($counter, $printDueDate)
    {
        echo '<div class="toDo" dueDate="' . $this->toDos[$counter]->dueDate . '">';
        echo '<input type="checkbox" onchange="toDoItemChanged(this)" />';
        echo '<span>' . $this->toDos[$counter]->description . '</span>';
        if ($printDueDate) {
            echo '<span> - ' . $this->toDos[$counter]->dueDateDisplay . '</span>';
        }
        echo '</div>';
    }

    public function printToDos() {
        foreach ($this->toDos as $toDo) {
            echo '<div class="toDo" dueDate="' . $toDo->dueDate . '">';
            echo '<input type="checkbox" onchange="toDoItemChanged(this)" />';
            echo '<span>' . $toDo->description . '</span>';
            echo '<span> - ' . $toDo->dueDateDisplay . '</span>';
            echo '</div>';
        }
    }

    public function printToDosInSections_Deprecated()
    {
        usort($this->toDos, 'compare');

        $day = date('j');
        $month = date('n');
        $year = date('Y');
        $daysInMonth = date('t');

        $today = date('Y-m-d');

        $day++;
        if ($day > $daysInMonth) {
            $day = 1;
            $month++;
            if ($month == 13) {
                $month = 1;
                $year++;
            }
        }
        $tomorrow = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));

        $day++;
        if ($day > $daysInMonth) {
            $day = 1;
            $month++;
            if ($month == 13) {
                $month = 1;
                $year++;
            }
        }
        $dayAfterTomorrow = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));

        echo '<div class="toDoSection" id="done">';
        echo '<h1>Erledigt</h1>';
        echo '</div>';

        $counter = 0;
        $count = count($this->toDos);

        echo '<div class="toDoSection" id="overdue">';
        echo '<h1>Überfällig</h1>';

        for (; $counter < $count; $counter++) {
            if ($this->toDos[$counter]->dueDate < $today) {
                $this->printToDo($counter, true);
            } else {
                break;
            }
        }
        echo '</div>';

        echo '<div class="toDoSection" id="today">';
        echo '<h1>Heute</h1>';
        for (; $counter < $count; $counter++) {
            if ($this->toDos[$counter]->dueDate == $today) {
                $this->printToDo($counter, false);
            } else {
                break;
            }
        }
        echo '</div>';

        echo '<div class="toDoSection" id="tomorrow">';
        echo '<h1>Morgen</h1>';
        for (; $counter < $count; $counter++) {
            if ($this->toDos[$counter]->dueDate == $tomorrow) {
                $this->printToDo($counter, false);
            } else {
                break;
            }
        }
        echo '</div>';

        echo '<div class="toDoSection" id="dayAfterTomorrow">';
        echo '<h1>Übermorgen</h1>';
        for (; $counter < $count; $counter++) {
            if ($this->toDos[$counter]->dueDate == $dayAfterTomorrow) {
                $this->printToDo($counter, false);
            } else {
                break;
            }
        }
        echo '</div>';

        echo '<div class="toDoSection" id="future">';
        echo '<h1>Zukünftig</h1>';
        for (; $counter < $count; $counter++) {
            if ($this->toDos[$counter]->dueDate > $dayAfterTomorrow) {
                $this->printToDo($counter, true);
            } else {
                break;
            }
        }
        echo '</div>';

        echo '<div class="toDoSection" id="noDueDate">';
        echo '<h1>Ohne Datum</h1>';
        for (; $counter < $count; $counter++) {
            $this->printToDo($counter, false);
        }
        echo '</div>';
    }

    public function printToDoSections()
    {
        echo '<div class="toDoSection" id="unsorted">';
        echo '<h1>Unsortiert</h1>';
        $this->printToDos();
        echo '</div>';

        echo '<div class="toDoSection" id="done">';
        echo '<h1>Erledigt</h1>';
        echo '</div>';

        echo '<div class="toDoSection" id="overdue">';
        echo '<h1>Überfällig</h1>';
        echo '</div>';

        echo '<div class="toDoSection" id="today">';
        echo '<h1>Heute</h1>';
        echo '</div>';

        echo '<div class="toDoSection" id="tomorrow">';
        echo '<h1>Morgen</h1>';
        echo '</div>';

        echo '<div class="toDoSection" id="dayAfterTomorrow">';
        echo '<h1>Übermorgen</h1>';
        echo '</div>';

        echo '<div class="toDoSection" id="future">';
        echo '<h1>Zukünftig</h1>';
        echo '</div>';

        echo '<div class="toDoSection" id="noDueDate">';
        echo '<h1>Ohne Datum</h1>';
        echo '</div>';
    }
}

?>