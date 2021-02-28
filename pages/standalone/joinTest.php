<!DOCTYPE html>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use \MongoDB\BSON\ObjectId as ObjectId;
use MathExam\ActiveTest as ActiveTest;
?>
<html>
    <head>
        <meta charset="utf-8" />
        <title>
            <?php echo $dictionary->title; ?>
        </title>

        <link href="./stylesheets/main.css"
              rel="stylesheet" type="text/css" />
        <link href="./stylesheets/joinTest.css"
              rel="stylesheet" type="text/css" />

        <script defer src="./scripts/visuals/ExtendedDimensionParser.js"></script>
        <script src="./scripts/StateTracker.js"></script>

        <script src="./scripts/mathjaxConfig.js"></script>
        <script async src="./mathjax/startup.js" id="MathJax-script"></script>

    </head>
    <body>
        <div class="notification">
            Начало:
            <br/>
            Край:
            <br/>
            Стартиране след:
            <br/>
            <div style="width: 100px; margin: 10px; text-align: center;  border: 1px solid rgb(0, 250, 0); ">
                Старт
            </div>
        </div>
        <?php
        $activeTest = null;
        $startsAfter = null;
        $endsAfter = null;

        if (isset($_GET["test"]) && is_string($_GET["test"])){
            $key = trim($_GET["test"]);
            $id = ActiveTest::getIdFromKey($db, $key);
            if (isset($id)){
                $activeTest = new ActiveTest($db, new ObjectId($id));
            }
        }

        if (isset($activeTest)){
            $startsAfter = $activeTest->start->toDateTime()->getTimestamp() - time();
            $endsAfter = $activeTest->end->toDateTime()->getTimestamp() - time();
        }

        echo "Съществуващо изпитване: " . ($activeTest ? "да" : "не") . "<br />";
        echo "Започва след: " . $startsAfter . "<br />";
        echo "Свършва след: " . $endsAfter . "<br />";
        ?>
    </body>
</html>
