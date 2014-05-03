<?php
$test = array();
$testEntry1 = array();
$testEntry2 = array();
$testEntry3 = array();

array_push($testEntry1, 10);
array_push($testEntry1, 20);

array_push($testEntry2, 30);
array_push($testEntry2, 40);

array_push($testEntry3, 50);
array_push($testEntry3, 60);


array_push($test, $testEntry1);
array_push($test, $testEntry2);
array_push($test, $testEntry3);

print_r($test);
echo '<br /><br />';
rsort($test);
print_r($test);
?>