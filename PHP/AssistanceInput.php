<?php


class AssistanceInput
{
    public $assistanceInput = array();
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

                if ($name != "") {
                    $this->assistanceInput[$name] = explode(";", $dates);
                    $this->dataExist = true;
                }
            }
            fclose($file);
        }
    }

    public function saveToFile()
    {
        $file = fopen($this->fileName, "w");

        foreach ($this->assistanceInput as $name => $dates) {
            fwrite($file, $name . "\n");
            fwrite($file, implode(";", $dates) . "\n");
        }

        fclose($file);
    }
}

?>