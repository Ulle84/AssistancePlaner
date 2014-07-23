<?php

class Settings
{
    public $standardPassword = "Hallo123";
    public $adminName = "Patrick"; //TODO dynamically!

    public $adminFirstName;
    public $adminLastName;

    public $showToDoManager;

    function __construct()
    {
        //$this->adminName = $_SESSION['client'];
    }
}

?>