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
        <link href="./stylesheets/FancyContextMenu.css"
              rel="stylesheet" type="text/css" />

        <script defer src="./scripts/visuals/ExtendedDimensionParser.js"></script>
        <script defer src="./scripts/visuals/SwidingBoard.js"></script>
        <script defer src="./scripts/visuals/FancyContextMenu.js"></script>
        <script src="./scripts/StateTracker.js"></script>

        <script>
            var TestsPageGUI = {
                logDownloadTestData: false,
                logCreateCommands: true,
                animationSpeed: 830,

                noTestName: "<i>Неименуван тест</i>",
                noTestDescription: "<i>Няма описание на теста</i>",

                noGroupName: "<i>Неименуван тест</i>",
                noGroupDescription: "<i>Няма описание на теста</i>",

                noExerciseName: "<i>Неименувана задача</i>",
                noExerciseDescription: "<i>Няма описание за тази задача</i>"
            }
        </script>

        <script defer src="./scripts/specific/TestsPageGUI/Container.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/exerciseContainer.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/groupsContainer.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/testsContainer.js"></script>

        <script defer src="./scripts/specific/TestsPageGUI/DefaultEditor.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/ContentListEditor.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/editTest.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/editGroup.js"></script>

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

        <div id="testEditorPage" class="page">
            <fieldset id="testDetails" class="details">
                <legend> Тест </legend>
                <fieldset class="textarea">
                    <legend> Име </legend>
                    <div id="testName" class="title"
                         contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea">
                    <legend> Описание </legend>
                    <div id="testDescription" class="description"
                         contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea selectedElementsList">
                    <legend> Групи в теста </legend>
                    <div id="testContents"></div>
                </fieldset>
            </fieldset>
            <fieldset class="editorContents"
                      data-dimensions="height: <#testDetails>.offsetHeight - 24;
                      width: <#testEditorPage>.offsetWidth - <#testDetails>.offsetWidth - 42;">
                <legend> Налични групи </legend>
                <div id="exerciseGroupsContainer" class="container"></div>
            </fieldset>
        </div>
        <div id="groupEditorPage" class="page">
            <fieldset id="groupDetails" class="details">
                <legend> Група </legend>
                <fieldset class="textarea">
                    <legend> Име </legend>
                    <div id="groupName" class="title"
                         contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea">
                    <legend> Описание </legend>
                    <div id="groupDescription" class="description"
                         contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea selectedElementsList">
                    <legend> Задачи в групата </legend>
                    <div id="groupContents"></div>
                </fieldset>
            </fieldset>
            <fieldset class="editorContents"
                      data-dimensions="height: <#groupDetails>.offsetHeight - 24;
                      width: <#groupEditorPage>.offsetWidth - <#groupDetails>.offsetWidth - 42;">
                <legend> Налични задачи </legend>
                <div id="exercisesContainer" class="container"></div>
            </fieldset>
        </div>
        <div id="exerciseExitorPage" class="page" >
            <fieldset id="exerciseDetails" class="details">
                <legend> Задача </legend>
                <fieldset class="textarea">
                    <legend> Име </legend>
                    <div id="exerciseName" class="title"
                         contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea">
                    <legend> Описание </legend>
                    <div id="exerciseDescription" class="description"
                         contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea selectedElementsList">
                    <legend> Поле за селекция на формули тук. </legend>
                </fieldset>
            </fieldset>
            <fieldset class="editorContents"
                      data-dimensions="height: <#exerciseDetails>.offsetHeight - 24;
                      width: <#exerciseExitorPage>.offsetWidth - <#exerciseDetails>.offsetWidth - 42;">
                <legend> Направи задача </legend>
            </fieldset>
        </div>

        <div id="testsContainer" class="container"
             data-dimensions="height: windowHeight - <#header>.offsetHeight - 10;" >
            <noscript>
            Разрешете JavaScript за правилното функциониране на сайта.
            </noscript>
        </div>
        <div id="testsShadow"
             data-dimensions="height: <#testsContainer>.offsetHeight;
             top: <#testsContainer>.offsetTop;" >
        </div>
    </body>
</html>