<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>PHP Test</title>
</head>
<body>

<?php
/**
 * Created by PhpStorm.
 * User: Ulle
 * Date: 17.04.14
 * Time: 09:35
 */

// this file is for testing only

// print text
$txt = "Hello world <br />";
echo $txt;

// array
$cars = array("Volvo", "BMW", "Toyota");
echo $cars[0], "<br />";

// data types
$x = 5985;
var_dump($x);

$x = 10.365;
var_dump($x);

$x = true;

var_dump($cars);

$x = null;

// constants
define("GREETING", "Hello world! <br />");
echo GREETING;

// string Concatenation
$str1 = "hello";
$str2 = "world";
$together = $str1 . $str2 . "<br />";

/* identical operator
$x === $y
True if $x is equal to $y, and they are of the same type
*/

// if elsif else
$t = date("H");
if ($t < "10") {
    echo "Have a good morning!";
} elseif ($t < "20") {
    echo "Have a good day!";
} else {
    echo "Have a good night!";
}

echo "<br />";

// switch
$favcolor = "red";
switch ($favcolor) {
    case "red":
        echo "Your favorite color is red!";
        break;
    case "blue":
        echo "Your favorite color is blue!";
        break;
    case "green":
        echo "Your favorite color is green!";
        break;
    default:
        echo "Your favorite color is neither red, blue, or green!";
}

// while
$x = 1;
while ($x <= 5) {
    echo "The number is: $x <br>";
    $x++;
}

// do while, for like c/c++

$colors = array("red", "green", "blue", "yellow");
foreach ($colors as $value) {
    echo "$value <br>";
}

// functions
function familyName($fname)
{
    echo "$fname Refsnes.<br>";
}

familyName("Jani");
familyName("Hege");

// default argument value
function setHeight($minheight = 50)
{
    echo "The height is : $minheight <br>";
}

setHeight();

// loop through an indexed array
$cars = array("Volvo", "BMW", "Toyota");
$arrlength = count($cars);

for ($x = 0; $x < $arrlength; $x++) {
    echo $cars[$x];
    echo "<br>";
}

// Loop Through an Associative Array
$age = array("Peter" => "35", "Ben" => "37", "Joe" => "43");

foreach ($age as $x => $x_value) {
    echo "Key=" . $x . ", Value=" . $x_value;
    echo "<br>";
}

/* SuperGlobals
$GLOBALS
$_SERVER
$_REQUEST
$_POST
$_GET
$_FILES
$_ENV
$_COOKIE
$_SESSION
*/

?>


</body>
</html>