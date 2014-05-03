<?php

/* autoloading */

function __autoload($className) {
    if (file_exists($className . '.php')) {
        require_once $className . '.php';
        return true;
    }
    return false;
}

class SimpleClass
{
    function __construct() {
        print "Im SimpleClass Konstruktor\n";
    }

    // Deklaration einer Eigenschaft
    public $var = 'ein Standardwert';

    // Deklaration einer Methode
    public function displayVar()
    {
        echo $this->var;
    }
}

class ExtendClass extends SimpleClass // only ONE baseclass!
{
    function __construct() {
        parent::__construct();
        print "Im ExtendClass Konstruktor\n";
    }

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

echo '<br /> $myExtendedTestClass: <br />';
print_r($myExtendedTestClass);

// instanceof gibt es :-)

?>