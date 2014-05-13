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

    public function printToDoSections()
    {
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

    public function printToDoTable()
    {
        echo '<table id="toDoData">';

        echo '<tr>';
        echo '<th>Description</th>';
        echo '<th>Due Date</th>';
        echo '</tr>';

        foreach ($this->toDos as $toDo) {
            echo '<tr>';
            echo '<td class="left">' . $toDo->description . '</td>';
            echo '<td>' . $toDo->dueDate . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    }
}

?>