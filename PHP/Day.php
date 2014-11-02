<?php

class Day
{
    public $dayNumber;
    public $weekday; // 1 = Monday, 2 = Tuesday, ...
    public $serviceBegin;
    public $serviceEnd;
    public $serviceHours;
    public $standbyHours;
    public $publicNotes;
    public $privateNotes;

    public function calculateWorkingHours()
    {
        $startTime = (int)substr($this->serviceBegin, 0, 2);
        $endTime = (int)substr($this->serviceEnd, 0, 2);

        $this->serviceHours = 24 - $startTime + $endTime - 6; // 6 hours during the night do not count

        if (substr($this->serviceBegin, 3, 2) == "30") {
            $this->serviceHours -= 0.5;
        }

        if (substr($this->serviceEnd, 3, 2) == "30") {
            $this->serviceHours += 0.5;
        }

        //TODO make configurabele
        if ($this->serviceHours <= 13) {
            $this->standbyHours = 0.5;
        } else {
            $this->standbyHours = 1.0;
        }
    }

    public function getWorkingHours()
    {
        return $this->serviceBegin . " - " . $this->serviceEnd;
    }
}

?>