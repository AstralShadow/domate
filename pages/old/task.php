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

        <script src="./scripts/StateTracker.js"></script>

        <script src="./scripts/specific/TaskPageUI.js"></script>
        <script async src="./mathjax/startup.js"id="MathJax-script"></script>

    </head>
    <body class="nomathjax">
        <fieldset class="textarea" style="width: 500px;">
            <legend> Условие </legend>
            <span class="mathjax" style="height:500px;width:500px;">
                `x=(-b +- sqrt(b^2 – 4ac))/(2a)`
            </span>
        </fieldset>

        <fieldset class="textarea" style="width: 500px;">
            <legend> Редактор </legend>
            <br />
            <div id="testDescription" contentEditable="true">
                `x=(-b +- sqrt(b^2 - 4ac))/(2a)`
            </div>
            <br />
            <span style="color:rgba(0, 255, 0, 0.5);">
                Моля оградете уравнението с `
            </span>
        </fieldset>
        <div style="width:500px;">
        </div>
    </body>
</html>