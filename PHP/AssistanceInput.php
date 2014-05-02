<?php


class AssistanceInput
{
    public $assistanceInput = array();

    public function readFromFile($fileName)
    {
        if (file_exists($fileName)) {
            $file = fopen($fileName, "r");
            $dateSheet = array();
            while (!feof($file)) {
                $name = rtrim(fgets($file));
                $dates = rtrim(fgets($file));

                if ($name != "") {
                    $this->assistanceInput[$name] = $dates;
                }
            }
            fclose($file);
        }
    }
}

?>