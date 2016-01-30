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
        if (substr($this->serviceBegin, 0, 2) == "--") {
            $this->standbyHours = 0;
            $this->serviceHours = 0;
            return;
        }

        $hoursStartTime = floatval(substr($this->serviceBegin, 0, 2));
        $minutesStartTime = floatval(substr($this->serviceBegin, 3, 2));
        $hoursEndTime = floatval(substr($this->serviceEnd, 0, 2));
        $minutesEndTime = floatval(substr($this->serviceEnd, 3, 2));

        $startTime = $hoursStartTime + $minutesStartTime / 60;
        $endTime = $hoursEndTime + $minutesEndTime / 60;

        /* old calculation before minimum wage
         *
         * $this->serviceHours = 24 - $startTime + $endTime - 6; // 6 hours during the night do not count

        //TODO make configurabele
        if ($this->serviceHours <= 13) {
            $this->standbyHours = 0.5;
        } else {
            $this->standbyHours = 1.0;
        }*/

        $this->serviceHours = 24 - $startTime + $endTime;

        //TODO make configurabele
        if ($this->serviceHours <= 19) {
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