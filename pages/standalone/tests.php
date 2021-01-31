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
        <link href="./stylesheets/tests.css"
              rel="stylesheet" type="text/css" />
        <link href="./stylesheets/alignedText.css"
              rel="stylesheet" type="text/css" />

        <script defer src="./scripts/visuals/ExtendedDimensionParser.js"></script>
        <script src="./scripts/StateTracker.js"></script>

        <script defer src="./scripts/specific/TestsPageGUI.js"></script>

    </head>
    <body>
        <div id="header">
            <div id="logo"></div>
            <div class="alignedTextContainer"
                 data-dimensions="width: <#header>.offsetWidth - <#logo>.offsetWidth - 75;">
                <div class="element left">
                    <div class="topic">
                        Как се създава тест? (или н.т.)
                    </div>
                    <br />
                    <div class="content">
                        <div style="float: right; min-width:50px; min-height:10px;"></div>
                        Тук виждате създадените от Вас тестове.
                        На всеки тест при съсдаването му можете да
                        напишете заглавие и описание. Като минете с
                        мишката върху даден тест можете да го
                        редактирате или....
                        (може би да го дават на учениците си?)
                        <div style="float:left; min-width:50px; min-height:10px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="testsContainer">
            <div class="contents">
                <div class="block">
                    <div class="name">
                        test
                    </div>
                    <div class="description">
                        В разработка
                    </div>
                </div>
            </div>
            <div class="newElementButton"></div>
        </div>
        <div id="editTest">
            <fieldset class="textarea">
                <legend> Име </legend>
                <div id="testName" contentEditable="true" ></div>
            </fieldset>
            <fieldset class="textarea">
                <legend> Описание </legend>
                <div id="testDescription" contentEditable="true" ></div>
            </fieldset>

        </div>
    </body>
</html>