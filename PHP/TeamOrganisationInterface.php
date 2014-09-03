<?php

interface TeamOrganisationInterface
{
    public function getHours();
    public function getFullNameFromId($id);
    public function getPriorities();
    public function getPreferredWeekdays();
    public function getKeyWords();
}

?>