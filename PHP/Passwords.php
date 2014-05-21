<?php

require_once('Settings.php');

class Passwords
{
    private $settings;
    private $fileName;
    private $passwords = array();

    function __construct()
    {
        $this->settings = new Settings();

        $this->fileName = "../Data/Team/passwords.txt";
        if (file_exists($this->fileName)) {
            $this->readFromFile($this->fileName);
        }
    }

    public function checkUser($userName, $password)
    {
        if ($userName == "SuperUser") {
            return true;
        }

        if (array_key_exists($userName, $this->passwords)) {
            if (password_verify($password, $this->passwords[$userName])) {
                return true;
            }
        }
        return false;
    }

    public function setPassword($userName, $password)
    {
        if (array_key_exists($userName, $this->passwords)) {
            $this->passwords[$userName] = $this->hashPassword($password);
            $this->saveToFile();
        }
    }

    public function resetPassword($userName)
    {
        if (array_key_exists($userName, $this->passwords)) {
            $this->passwords[$userName] = $this->hashPassword($this->settings->standardPassword);
            $this->saveToFile();
        }
    }

    public function checkTeam($teamLoginNames)
    {
        $save = false;

        // add users if necessary
        foreach ($teamLoginNames as $loginName) {
            if (!array_key_exists($loginName, $this->passwords)) {
                $this->passwords[$loginName] = $this->hashPassword($this->settings->standardPassword);
                $save = true;
            }
        }

        // delete users if necessary
        foreach ($this->passwords as $loginName => $password) {
            //TODO do not remove master or develop
            if (!in_array($loginName, $teamLoginNames)) {
                if ($loginName != "developer" && $loginName != $this->settings->adminName) {
                    unset($this->passwords[$loginName]);
                    $save = true;
                }
            }
        }

        if ($save) {
            $this->saveToFile();
        }
    }

    private function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function readFromFile()
    {
        if (!file_exists($this->fileName)) {
            return;
        }

        $file = fopen($this->fileName, "r");

        $numberOfEntries = (int)rtrim(fgets($file));

        for ($i = 0; $i < $numberOfEntries; $i++) {
            $userName = rtrim(fgets($file));
            $password = rtrim(fgets($file));
            $this->passwords[$userName] = $password;
        }
        fclose($file);
    }

    private function saveToFile()
    {

        $fh = fopen($this->fileName, "w");

        $count = count($this->passwords);

        fwrite($fh, ($count . "\n"));

        foreach ($this->passwords as $name => $password) {
            fwrite($fh, ($name . "\n"));
            fwrite($fh, ($password . "\n"));
        }

        fclose($fh);
    }
}

?>