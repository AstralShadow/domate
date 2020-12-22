<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/task.php";
require "include/interpreter.php";

$task = new \Main\Interpreter\Task();
$task->description = 'Колко е {a} + {b}?';
$task->code = <<<EOD2
answer = random(0, 10)
a = random(0, answer)
b = answer - a
EOD2;

echo "Пробна задача:<br />";
var_dump($task);

$interpreter = new \Main\Interpreter\Interpreter($task);
// foreach (["text", "answer2", "a", "b", "c"] as $name){
//     $var = $interpreter->getVariable($name);
//     echo $var->name . " = " . $var->value . "<br />";
// }


echo "5 случайни варианта:";
for ($i = 0; $i < 5; $i++){
    $variant = $interpreter->generateVariant();
    echo "Условие: ";
    var_dump($variant->description);
    echo "Отговор: ";
    var_dump($variant->answer);
}
?>

