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
                    array_push($this->toDos, $toDoItem);

                    $this->dataExist = true;
                }
            }
            fclose($file);
        }
    }

    public function printToDos()
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
                echo '<div class="toDo"><input type="checkbox" onchange="toDoItemChanged(this)" /><span>' . $this->toDos[$counter]->description . ' (' . $this->toDos[$counter]->dueDate . ')</span></div>';
            } else {
                break;
            }
        }
        echo '</div>';

        echo '<div class="toDoSection" id="today">';
        echo '<h1>Heute</h1>';
        for (; $counter < $count; $counter++) {
            if ($this->toDos[$counter]->dueDate == $today) {
                echo '<div class="toDo"><input type="checkbox" onchange="toDoItemChanged(this)" /><span>' . $this->toDos[$counter]->description . '</span></div>';
            } else {
                break;
            }
        }
        echo '</div>';

        echo '<div class="toDoSection" id="tomorrow">';
        echo '<h1>Morgen</h1>';
        for (; $counter < $count; $counter++) {
            if ($this->toDos[$counter]->dueDate == $tomorrow) {
                echo '<div class="toDo"><input type="checkbox" onchange="toDoItemChanged(this)" /><span>' . $this->toDos[$counter]->description . '</span></div>';
            } else {
                break;
            }
        }
        echo '</div>';

        echo '<div class="toDoSection" id="dayAfterTomorrow">';
        echo '<h1>Übermorgen</h1>';
        for (; $counter < $count; $counter++) {
            if ($this->toDos[$counter]->dueDate == $dayAfterTomorrow) {
                echo '<div class="toDo"><input type="checkbox" onchange="toDoItemChanged(this)" /><span>' . $this->toDos[$counter]->description . '</span></div>';
            } else {
                break;
            }
        }
        echo '</div>';

        echo '<div class="toDoSection" id="future">';
        echo '<h1>Zukünftig</h1>';
        for (; $counter < $count; $counter++) {
            if ($this->toDos[$counter]->dueDate > $dayAfterTomorrow) {
                echo '<div class="toDo"><input type="checkbox" onchange="toDoItemChanged(this)" /><span>' . $this->toDos[$counter]->description . ' (' . $this->toDos[$counter]->dueDate . ')</span></div>';
            } else {
                break;
            }
        }
        echo '</div>';

        echo '<div class="toDoSection" id="noDueDate">';
        echo '<h1>Ohne Datum</h1>';
        for (; $counter < $count; $counter++) {
            echo '<div class="toDo"><input type="checkbox" onchange="toDoItemChanged(this)" /><span>' . $this->toDos[$counter]->description . '</span></div>';
        }
        echo '</div>';
    }
}

?>