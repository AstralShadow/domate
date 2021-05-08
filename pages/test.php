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
use MathExam\TestSolution as TestSolution;

$_key;

$active_exam = null;
$test = null;
$startsAfter = null;
$endsAfter = null;
$errorMsg = null;

$id = ActiveTest::getIdFromKey($db, $_key);
if ($id == null){
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
} else {
    $activeTest = new ActiveTest($db, new ObjectId($id));
}

if (!isset($activeTest)){
    $errorMsg = $dictionary["join_test"]["not_existing"];
}

if (isset($activeTest)){
    $startTimestamp = $activeTest->start->toDateTime()->getTimestamp();
    $endTimestamp = $activeTest->end->toDateTime()->getTimestamp();

    if (Test::exists($db, new ObjectId($activeTest->test))){
        $test = new Test($db, new ObjectId($activeTest->test));
    } else {
        $errorMsg = $dictionary["join_test"]["source_deleted"];
    }

    /* Already started test */

    $activeTests = $session->solutions;
    if (isset($activeTests[$_key])){
        $myTestId = $activeTests[$_key];
        $myTest = new TestSolution($db, $activeTests[$_key]);
        $endTimestamp = $myTest->finished->toDateTime()->getTimestamp();
    }

    $startsAfter = $startTimestamp - time();
    $endsAfter = $endTimestamp - time();

    if ($endsAfter < 0){
        $errorMsg = $dictionary["join_test"]["already_expired"];
    }

    /* Worktime and Task count strings */
    $worktime = "(";
    if ($activeTest->worktime > 60){
        $worktime .= floor($activeTest->worktime / 60) . " ";
        if ($activeTest->worktime < 120){
            $worktime .= $dictionary["hour"];
        } else {
            $worktime .= $dictionary["hours"];
        }
        if ($activeTest->worktime % 60){
            $worktime .= " " . $dictionary["and"] . " ";
        }
    }
    if ($activeTest->worktime % 60){
        $worktime .= ($activeTest->worktime % 60) . " ";
        if ($activeTest->worktime % 60 == 1){
            $worktime .= $dictionary["minute"];
        } else {
            $worktime .= $dictionary["minutes"];
        }
    }
    $worktime .= ')';
    $questions = "(" . count($test->contents) . " ";
    if (count($test->contents) > 1){
        $questions .= $dictionary["question"];
    } else {
        $questions .= $dictionary["questions"];
    }
    $questions .= ')';
}
?>
<html>
    <head>
        <meta charset="utf-8" />
        <title>
            <?php echo $dictionary["title"]; ?>
        </title>

        <link href="./stylesheets/main.css"
              rel="stylesheet" type="text/css" />
        <link href="./stylesheets/joinTest.css"
              rel="stylesheet" type="text/css" />

        <script defer src="./scripts/visuals/ExtendedDimensionParser.js"></script>

        <script>
            var SolveTestGUI = {
                key: <?php echo json_encode($_key); ?>,
                endpoint: <?php echo json_encode("solve/" . $_key); ?>
            }
        </script>
        <script defer src="./scripts/specific/SolveTestGUI/getToken.js"></script>
        <script defer src="./scripts/specific/SolveTestGUI/Core.js"></script>
        <script defer src="./scripts/specific/SolveTestGUI/Timer.js"></script>
        <script defer src="./scripts/specific/SolveTestGUI/Progress.js"></script>
        <script defer src="./scripts/specific/SolveTestGUI/Tasks.js"></script>
        <script defer src="./scripts/specific/SolveTestGUI/start.js"></script>

        <script src="./scripts/mathjaxConfig.js"></script>
        <script async src="./mathjax/startup.js" id="MathJax-script"></script>

    </head>
    <body>
        <?php
        if (!$errorMsg && isset($activeTest) && !isset($myTestId)){
            // TODO abstract these things to use the api's GET
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
                    <?php echo $dictionary["STARTING_IN"]; ?>:
                    <div id="startTimer" class="timerFont">
                        ??:??:??
                    </div>
                    <script>
                        var startCountdownInterval = setInterval(progressStartTimer, 1000)
                        window.addEventListener("load", progressStartTimer)
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
                    <?php echo $dictionary["start"]; ?>
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

                            window.SolveTestGUI.start(identification, function (e) {
                                document.getElementById("startFeedback").innerText = e.message
                                setTimeout(function () {
                                    document.getElementById("startFeedback").innerText = ""
                                }, 5000)
                                if (e.code === "success") {
                                    started = true
                                    document.querySelector("#startMenu").style.display = "none"
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
                    window.SolveTestGUI.start(<?php echo json_encode((string) $_key); ?>, () => {
                    })
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
            <div class="title UIheader" ></div>

            <div class="small UIheader">
                <?php echo $questions . "<br />" . $worktime; ?>
            </div>
            <div id="mainTimer">
                <?php echo $dictionary["remaining_time"]; ?>: 
                <div class="timerFontHalf"></div>
            </div>

            <div id="progressDisplay"></div>
        </div>
        <?php
        if (isset($test)){
            ?>
            <script>
                window.addEventListener("load", fillTitle)
                fillTitle()
                function fillTitle () {
                    document.querySelectorAll(".title").forEach(function (el) {
                        el.innerText = <?php echo json_encode($test->name); ?> || "???"
                    })
                }
            </script>
            <?php
        }
        ?>

        <div id="testContents">
            <!--
            <div class="task">
                <div class="mathjax">Въпрос</div>
                <div class="textarea answer">
                    <div class="workarea mathjax">`x^2`</div>
                    <input type="text" class="input answerInput" value="x^2">
                </div>
            </div>
            -->
        </div>
    </body>
</html>
