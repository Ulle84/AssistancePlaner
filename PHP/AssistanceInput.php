<?php


class AssistanceInput
{
    public $assistanceInput = array();
    public $assistanceNotes = array();
    public $dataExist = false;
    private $fileName;

    function __construct($year, $month)
    {
        $this->fileName = "../Data/AssistanceInput/" . $year . "-" . $month . ".txt";
        $this->readFromFile();
    }

    private function readFromFile()
    {
        if (file_exists($this->fileName)) {
            $this->assistanceInput = array();

            $file = fopen($this->fileName, "r");

            while (!feof($file)) {
                $name = rtrim(fgets($file));
                $dates = rtrim(fgets($file));
                $notes = rtrim(fgets($file));

                if ($name != "") {
                    $this->assistanceInput[$name] = explode(";", $dates);
                    $this->assistanceNotes[$name] = $notes;
                    $this->dataExist = true;
                }
            }
            fclose($file);
        }
    }

    public function saveToFile()
    {
        $filePath = substr($this->fileName, 0, strrpos($this->fileName, '/'));

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }

        $file = fopen($this->fileName, "w");

        foreach ($this->assistanceInput as $name => $dates) {
            fwrite($file, $name . "\n");
            fwrite($file, implode(";", $dates) . "\n");
            fwrite($file, $this->assistanceNotes[$name] . "\n");
        }

        fclose($file);
    }

    public function hasNotes()
    {
        foreach ($this->assistanceNotes as $name => $note)
        {
            if ($note != "")
            {
                return true;
            }
        }
        return false;
    }
}

?>