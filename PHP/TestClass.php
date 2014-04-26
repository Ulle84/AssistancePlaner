<?php

class SimpleClass
{
    // Deklaration einer Eigenschaft
    public $var = 'ein Standardwert';

    // Deklaration einer Methode
    public function displayVar()
    {
        echo $this->var;
    }
}

class ExtendClass extends SimpleClass
{
    // Die Vatermethode überschreiben
    function displayVar()
    {
        echo "Erweiternde Klasse\n";
        parent::displayVar();
    }
}

$myTestClass = new SimpleClass();
$myTestClass->displayVar();

$myExtendedTestClass = new ExtendClass();
$myExtendedTestClass->displayVar();

?>