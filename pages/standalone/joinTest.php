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
        <div class="notification" style="display: none;">
            <div style="font-size: 25pt; text-align: center;">Математика</div>
            <div style="font-size: 12pt; text-align: center;">
                (<span>23</span> въпроси)
                <br/>
                (<span>90</span> минути)
            </div>
            <br/>
            <div style="display: ; text-align: center; font-size: 16pt;">
                ЗАПОЧВА СЛЕД:
                <br/>
                <div style="font-size: 45pt;">
                    33:33:33
                </div>
            </div>
            <label>
                <fieldset class="textarea"  style="padding: 5px; font-size: 16pt; margin: 5px; width:auto;">
                    <legend>
                        Име
                    </legend>
                    <input id="" style="border: 0px solid black;"
                           type="text" class="input">
                </fieldset>
            </label>
            <br/>
            <div style="
                 width: 100px;
                 margin: auto auto;
                 margin-top: 5px;
                 text-align: center; 
                 border: 1px solid rgb(0, 250, 0); ">
                Начало
            </div>
        </div>
        <div>
            <div style="font-size: 25pt; text-align: center;">Математика</div>
            <div style="font-size: 12pt; text-align: center;">
                (<span>23</span> въпроси)
                <br/>
                (<span>90</span> минути)
            </div>
            <div style=" text-align: center; font-size: 16pt; position: fixed; top: 5px; right: 5px;">
                Оставащо време:
                <br/>
                <div style="font-size: 45pt;">
                    33:33:33
                </div>
            </div>
        </div>
        <br/>
        <div style="padding: 5px; background-color: black; position: fixed; right: 10px; border: 2px solid rgb(0, 250, 0); border-radius: 40px 40px 40px 40px/15px 15px 15px 15px; width: 50px;">
            <div class="kryg">
            </div>
            <div class="kryg">
            </div>
            <div class="kryg">
            </div>
            <div class="kryg">
            </div>
            <div class="kryg">
            </div>
            <div class="kryg">
            </div>
        </div>
        <div id="fulltest">
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
