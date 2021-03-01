<!DOCTYPE html>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include "include/testsAndTasks.php";

use \MongoDB\BSON\ObjectId as ObjectId;
use MathExam\Test as Test;
use MathExam\ActiveTest as ActiveTest;

$activeTest = null;
$test = null;
$startsAfter = null;
$endsAfter = null;
$errorMsg = null;

if (isset($_GET["test"]) && is_string($_GET["test"])){
    $key = trim($_GET["test"]);
    $id = ActiveTest::getIdFromKey($db, $key);
    if (isset($id)){
        $activeTest = new ActiveTest($db, new ObjectId($id));
    }
}
if (!isset($activeTest)){
    $errorMsg = $dictionary->joinTest["notExisting"];
}

if (isset($activeTest)){
    $startTimestamp = $activeTest->start->toDateTime()->getTimestamp();
    $endTimestamp = $activeTest->end->toDateTime()->getTimestamp();
    $startsAfter = $startTimestamp - time();
    $endsAfter = $endTimestamp - time();

    if (Test::exists($db, new ObjectId($activeTest->test))){
        $test = new Test($db, new ObjectId($activeTest->test));
    } else {
        $errorMsg = $dictionary->joinTest["sourceDeleted"];
    }
    if ($endsAfter < 0){
        $errorMsg = $dictionary->joinTest["alreadyExpired"];
    }

    /* Worktime and Task count strings */
    $worktime = "(";
    if ($activeTest->worktime > 60){
        $worktime .= floor($activeTest->worktime / 60) . " час";
        if ($activeTest->worktime >= 120){
            $worktime .= "а";
        }
        if ($activeTest->worktime % 60){
            $worktime .= " и ";
        }
    }
    if ($activeTest->worktime % 60){
        $worktime .= ($activeTest->worktime % 60) . " минути";
    }
    $worktime .= ')';
    $questions = "(" . count($test->contents) . " въпрос";
    if (count($test->contents) > 1){
        $questions .= "а";
    }
    $questions .= ')';

    /* Already started test */

    $activeTests = $session->activeTests;
    if (isset($activeTests[$key])){
        $myTestId = $activeTests[$key];
    }
}
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

        <script defer src="./scripts/specific/SolveTestGUI/Core.js"></script>
        <script defer src="./scripts/specific/SolveTestGUI/Timer.js"></script>
        <script defer src="./scripts/specific/SolveTestGUI/Progress.js"></script>
        <script defer src="./scripts/specific/SolveTestGUI/start.js"></script>

        <script src="./scripts/mathjaxConfig.js"></script>
        <script async src="./mathjax/startup.js" id="MathJax-script"></script>

    </head>
    <body>
        <?php
        if (!$errorMsg && isset($activeTest) && !isset($myTestId)){
            ?>
            <div class="centeredBox" id="startMenu" >
                <div class="title" ></div>
                <div class="small">
                    <?php echo $questions . "<br />" . $worktime; ?>
                </div>
                <?php
                $timerCss = "style=\"display: none;\" ";
                if (isset($startsAfter) && $startsAfter > 0){
                    $timerCss = "";
                }
                ?>
                <script>
                    const startsAt = <?php echo json_encode($startTimestamp); ?>
                </script>
                <div id="startTimerBox" <?php echo $timerCss; ?>>
                    ЗАПОЧВА СЛЕД:
                    <div id="startTimer" class="timerFont">
                        ??:??:??
                    </div>
                    <script>
                        var startCountdownInterval = setInterval(progressStartTimer, 1000)
                        progressStartTimer()
                        function progressStartTimer () {
                            "use strict"
                            const div = document.getElementById("startTimer");
                            var delta = startsAt - (new Date()).getTime() / 1000
                            var difference = Math.abs(Math.floor(delta))
                            var hours = "00", minutes = "00", seconds = "00"
                            if (Math.abs(difference) >= 3600) {
                                hours = Math.floor(difference / 3600)
                            }
                            if (Math.abs(difference) % 3600 >= 60) {
                                minutes = Math.floor(difference / 60) % 60
                            }
                            if (difference % 60) {
                                seconds = difference % 60
                            }

                            hours = String(hours).padStart(2, '0')
                            minutes = String(minutes).padStart(2, '0')
                            seconds = String(seconds).padStart(2, '0')
                            div.innerText = (delta < 0 ? "-" : "") + hours + ':' + minutes + ':' + seconds
                            if (delta <= 0 && window.readyFunc) {
                                document.getElementById("startTimerBox").style.display = "none"
                                if (window.readyFunc()) {
                                    clearInterval(startCountdownInterval)
                                }
                            }
                        }
                    </script>
                </div>
                <?php
                if ($activeTest->question){
                    ?>
                    <label>
                        <fieldset class="textarea">
                            <legend id="identificationQuestion"></legend>
                            <script>
                                document.querySelector("#identificationQuestion")
                                    .innerText = <?php echo json_encode($activeTest->question) ?>
                            </script>
                            <input type="text" id="identification" class="input">
                        </fieldset>
                    </label>
                    <?php
                }
                ?>
                <div id="startButton">
                    Начало
                </div>
                <span id="startFeedback"></span>
                <script>
                    window.addEventListener("load", function () {
                        'use strict'
                        const id = document.querySelector("#identification")
                        window.readyFunc = function () {
                            enableStart()
                            return true
                        }
                        const start = document.getElementById("startButton")
                        function enableStart () {
                            start.style.opacity = 1
                            start.style.cursor = "pointer"
                            start.addEventListener("click", function () {
                                if (id && !id.value.length) {
                                    id.style.border = "1px solid red"
                                    return;
                                }
                                attemptStart()
                            })
                        }
                        var started = false
                        function attemptStart () {
                            var identification
                            if (id) {
                                identification = id.value
                            }
                            if (started) {
                                return;
                            }
                            var testKey = <?php echo json_encode($_GET["test"]); ?>

                            window.SolveTestGUI.start(testKey, identification, function (e) {
                                document.getElementById("startFeedback").innerText = e.msg
                                setTimeout(function () {
                                    document.getElementById("startFeedback").innerText = ""
                                }, 5000)
                                if (e.code === "Success") {
                                    started = true
                                    document.querySelector("#startMenu").display = "none"
                                }
                            })

                        }
                    })
                </script>
            </div>
            <?php
        }
        if (!$errorMsg && isset($myTestId)){
            ?>
            <script defer>
                window.addEventListener("load", function () {
                    new window.SolveTestGUI.Core(<?php echo json_encode((string) $myTestId); ?>)
                })
            </script>
            <?php
        }
        if ($errorMsg){
            ?>
            <div class="centeredBox" style="font-size: 18pt;">
                <?php echo $errorMsg; ?>
            </div>
            <?php
        }
        ?>
        <div id="testUI" style="display: none;">
            <div class="title" ></div>

            <div class="small">
                <?php echo $questions . "<br />" . $worktime; ?>
            </div>
            <div id="mainTimer">
                Оставащо време: 
                <div class="timerFontHalf"></div>
            </div>

            <div id="progressDisplay">
                <div class="circle"></div>
            </div>
        </div>
        <script>
            window.addEventListener("load", fillTitle)
            fillTitle()
            function fillTitle () {
                document.querySelectorAll(".title").forEach(function (el) {
                    el.innerText = <?php echo json_encode($test->name) ?>
                })
            }
        </script>
        <br/>
        <div id="fulltest" style="display: none;">
            <fieldset id="test">  
                <label>
                    <fieldset class="textarea"  style="border-top: 4px solid rgb(0, 250, 0); padding: 5px; font-size: 16pt; margin: 2px; width:auto;">
                        <legend>
                            Oтговор
                        </legend>
                        <input id="" style=" border: 0px solid black;"
                               type="text" class="input">
                    </fieldset>
                </label>
            </fieldset>
            <fieldset id="test">  
                <label>
                    <fieldset class="textarea"  style="border-top: 4px solid rgb(0, 250, 0); padding: 5px; font-size: 16pt; margin: 2px; width:auto;">
                        <legend>
                            Oтговор
                        </legend>
                        <input id="" style=" border: 0px solid black;"
                               type="text" class="input">
                    </fieldset>
                </label>
            </fieldset>
            <fieldset id="test">  
                <label>
                    <fieldset class="textarea"  style="border-top: 4px solid rgb(0, 250, 0); padding: 5px; font-size: 16pt; margin: 2px; width:auto;">
                        <legend>
                            Oтговор
                        </legend>
                        <input id="" style=" border: 0px solid black;"
                               type="text" class="input">
                    </fieldset>
                </label>
            </fieldset>
        </div>
    </body>
</html>
