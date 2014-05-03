<?php


class AssistanceInput
{
    public $assistanceInput = array();

    function __construct($year, $month)
    {
        $this->readFromFile("../Data/AssistanceInput/" . $year . "-" . $month . ".txt");
    }

    public function readFromFile($fileName)
    {
        if (file_exists($fileName)) {
            $this->assistanceInput = array();

            $file = fopen($fileName, "r");

            while (!feof($file)) {
                $name = rtrim(fgets($file));
                $dates = rtrim(fgets($file));

                if ($name != "") {
                    $this->assistanceInput[$name] = explode(";", $dates);
                }
            }
            fclose($file);
        }
    }
}

?>