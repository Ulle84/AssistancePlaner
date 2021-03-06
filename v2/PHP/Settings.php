<?php

include_once 'SettingsInterface.php';

class Settings implements SettingsInterface
{
    public $standardPassword;
    public $adminName;

    public $adminFirstName;
    public $adminLastName;
    public $mailAddress;
    public $firstNameProvider;
    public $lastNameProvider;
    public $mailAddressProvider;

    public $showToDoManager;

    private $fileName;

    function __construct($adminName)
    {
        $this->adminName = $adminName;

        $this->fileName = "../Data/" . strtolower($adminName) . "/Organization/settings.txt";
        $this->readFromFile();
    }

    public function getAdminName() {
        return $this->adminName;
    }

    public function readFromFile()
    {
        $defaultPassword = "Hallo123";

        if (file_exists($this->fileName)) {
            $file = fopen($this->fileName, "r");

            $this->adminFirstName = rtrim(fgets($file));
            $this->adminLastName = rtrim(fgets($file));
            $this->showToDoManager = rtrim(fgets($file));
            $this->mailAddress = rtrim(fgets($file));
            $this->standardPassword = rtrim(fgets($file));

            $this->firstNameProvider = rtrim(fgets($file));
            $this->lastNameProvider = rtrim(fgets($file));
            $this->mailAddressProvider = rtrim(fgets($file));

            if ($this->standardPassword == "") {
                $this->standardPassword = $defaultPassword;
            }
        }
        else {
            $this->standardPassword = $defaultPassword;
        }
    }
}

?>