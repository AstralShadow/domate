<!DOCTYPE html>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<html>
    <head>
        <meta charset="utf-8" />
        <title>
            <?php echo $dictionary->title; ?>
        </title>

        <link href="./stylesheets/main.css"
              rel="stylesheet" type="text/css" />
        <link href="./stylesheets/task.css"
              rel="stylesheet" type="text/css" />

        <script src="./scripts/StateTracker.js"></script>

        <script defer src="./scripts/specific/TaskPageUI.js"></script>
        <script async src="./mathjax/startup.js"id="MathJax-script"></script>

    </head>
    <body class="nomathjax">
        Задача:
        <span class="mathjax" style="height:500px;width:500px;">
            `x=(-b +- sqrt(b^2 – 4ac))/(2a)`
        </span>

        <fieldset class="textarea" style="width: 500px;">
            <legend> Задача </legend>
            <br />
            <div id="testDescription" contentEditable="true">
                `x=(-b +- sqrt(b^2 - 4ac))/(2a)`
            </div>
            <br />
            <span style="color:rgba(0, 255, 0, 0.5);">
                Моля оградете уравнението с `
            </span>
        </fieldset>
    </body>
</html>