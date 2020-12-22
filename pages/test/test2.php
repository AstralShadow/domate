<?php

include "include/classes/Interpreter/variableTypes.php";
require "include/classes/Interpreter/Operation.php";
require "include/classes/Interpreter/Operations/MathematicOperation.php";
require "include/classes/Interpreter/Operations/Addition.php";
require "include/classes/Interpreter/Operations/Subtraction.php";

$potential1 = new \Main\Interpreter\PotentialNumber(5, 9, 2);
$potential2 = new \Main\Interpreter\PotentialNumber(1, 5, 2);
$operation = new \Main\Interpreter\Operations\Subtraction();
$answer = $operation->execute($potential1, $potential2);
echo "Min: " . $answer->minValue . "\n";
echo "Max: " . $answer->maxValue . "\n";
for ($i = $answer->minValue; $i <= $answer->maxValue; $i += 1){
    $t = microtime(true);
    $show = $answer->contains($i);
    echo (microtime(true) - $t);
    if ($show)
        echo " - " . $i;
    echo "\n";
}
