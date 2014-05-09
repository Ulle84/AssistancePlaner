<?php

class MonthNavigation
{
    private $baseName;
    private $year;
    private $month;

    function __construct($baseName, $year, $month) {
        $this->baseName = $baseName;
        $this->year = $year;
        $this->month = $month;

        $this->printPrevious();
        $this->printSpace();
        $this->printCurrent();
        $this->printSpace();
        $this->printNext();
        echo '<br />';
    }

    private function printSpace() {
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
    }

    private function printPrevious()
    {
        $year = $this->year;
        $month = $this->month - 1;
        if ($month == 0) {
            $month = 12;
            $year--;
        }
        echo '<a href="' . $this->baseName . '?year=' . $year . '&month=' . $month . '">Vorheriger Monat</a>';
    }

    private function printNext()
    {
        $year = $this->year;
        $month = $this->month + 1;
        if ($month == 13) {
            $month = 1;
            $year++;
        }
        echo '<a href="' . $this->baseName . '?year=' . $year . '&month=' . $month . '">Nächster Monat</a>';
    }

    private function printCurrent()
    {
        $month = date("n");
        $year = date("Y");
        echo '<a href="' . $this->baseName . '?year=' . $year . '&month=' . $month . '">Aktueller Monat</a>';
    }
}

?>