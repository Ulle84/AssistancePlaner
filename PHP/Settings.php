<?php

class Settings
{
    public $standardPassword = "Hallo123";
    public $adminName;

    public $adminFirstName;
    public $adminLastName;
    public $mailAddress;
    public $showToDoManager;

    private $fileName;

    function __construct($adminName)
    {
        $this->adminName = $adminName;

        $this->fileName = "../Data/" . $adminName . "/Organization/settings.txt";
        $this->readFromFile();
    }

    public function readFromFile()
    {
        if (file_exists($this->fileName)) {
            $file = fopen($this->fileName, "r");

            $this->adminFirstName = rtrim(fgets($file));
            $this->adminLastName = rtrim(fgets($file));
            $this->showToDoManager = rtrim(fgets($file));
            $this->mailAddress = rtrim(fgets($file));
            $this->standardPassword = rtrim(fgets($file));
        }
    }
}

?>