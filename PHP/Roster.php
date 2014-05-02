<?php

require_once 'AssistanceInput.php';
require_once 'Team.php';
require_once 'MonthPlan.php';


class Roster
{
    private $month;
    private $year;
    private $daysPerMonth;
    private $team;
    private $assistanceInput;
    private $monthPlan;

    function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
        $this->daysPerMonth = date("t", mktime(0, 0, 0, $month, 1, $year));

        $this->team = new Team();
        $this->assistanceInput = new AssistanceInput();
        $this->monthPlan = new MonthPlan($year, $month);
        //TODO continue with implementation
    }
}

?>