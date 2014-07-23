<?php

class Settings
{
    public $standardPassword = "Hallo123";
    public $adminName;

    public $adminFirstName;
    public $adminLastName;

    public $showToDoManager;

    function __construct($adminName)
    {
        $this->adminName = $adminName;
    }
}

?>