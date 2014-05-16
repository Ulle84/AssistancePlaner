<?php

require_once('ToDoItem.php');

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
                $repetition = rtrim(fgets($file));
                $emptyLine = fgets($file);

                if ($description != "") {
                    $toDoItem = new ToDoItem();
                    $toDoItem->description = $description;
                    $toDoItem->dueDate = $dueDate;
                    $toDoItem->repetition = $repetition;
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
        echo '<div class="developerSection">';
        echo '<table id="toDoData"';
        if (!$_SESSION['developer']) {
            echo ' class="hidden"';
        }
        echo '>';

        echo '<tr>';
        echo '<th>Description</th>';
        echo '<th>Due Date</th>';
        echo '<th>Repetition</th>';
        echo '</tr>';

        foreach ($this->toDos as $toDo) {
            echo '<tr>';
            echo '<td class="left">' . $toDo->description . '</td>';
            echo '<td class="left">' . $toDo->dueDate . '</td>';
            echo '<td class="left">' . $toDo->repetition . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</div>';
    }

    public function printToDoInput()
    {
        echo '<div id="toDoInput">';

        echo '<span>Neue Aufgabe: </span>';
        echo '<input id="descriptionInput" type="text" size="20" maxlength="150" onchange="descriptionChanged()" onblur="descriptionChanged()"/>';

        echo '<span id="dueDate" class="hidden">';
        echo '<span> fällig am </span>';
        echo '<input id="dueDateInput" type="text" size="10" maxlength="10" onchange="dueDateChanged()" onblur="dueDateChanged()"/>';
        echo '</span>';

        echo '<span id="intervalNumber" class="hidden">';
        echo '<span> wiederholt sich </span>';

        echo '<select id="intervalNumberSelection" size="1" onchange="intervalNumberChanged()">';
        echo '<option id="never">nie</option>';
        echo '<option id="every">jeden</option>';
        echo '<option>alle 2</option>';
        echo '<option>alle 3</option>';
        echo '<option>alle 4</option>';
        echo '<option>alle 5</option>';
        echo '<option>alle 6</option>';
        echo '<option>alle 7</option>';
        echo '<option>alle 8</option>';
        echo '<option>alle 9</option>';
        echo '<option>alle 10</option>';
        echo '<option>alle 11</option>';
        echo '<option>alle 12</option>';
        echo '</select>';
        echo '</span>';

        echo '<span id="intervalType" class="hidden">';
        echo '<select id="intervalTypeSelection" size="1" onchange="intervalTypeChanged()">';
        echo '<option id="day">Tag</option>';
        echo '<option id="week">Woche</option>';
        echo '<option id="month">Monat</option>';
        echo '<option id="year">Jahr</option>';
        echo '</select>';
        echo '</span>';


        echo '<span id="repeatFrom" class="hidden">';
        echo '<span> ab dem </span>';
        echo '<select id="repeatFromSelection" size="1">';
        echo '<option>Erledigungsdatum</option>';
        echo '<option>Fälligkeitsdatum</option>';
        echo '</select>';
        echo '</span>';

        echo '<br />';
        echo '<input type="button" value="Hinzufügen" onclick="addToDo()" />';
        echo '</div>';
    }
}

?>