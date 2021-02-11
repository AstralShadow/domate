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
        <script defer src="./scripts/visuals/SwidingBoard.js"></script>
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


        <div id="editTestPage" style="width: 100%;">
            <fieldset id="editTest" style="background-color: rgb(0, 20, 0);">
                <legend> Тест </legend>
                <fieldset class="textarea" style="background-color: rgb(0, 40, 0);">
                    <legend> Име </legend>
                    <div id="testName" contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea" style="margin-top: 5px; background-color: rgb(0, 40, 0);">
                    <legend> Описание </legend>
                    <div id="testDescription" contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea" style=" overflow-y: scroll;  margin-top: 5px; background-color: rgb(0, 40, 0); height: 300px;">
                    <legend> Групи в теста </legend>
                    <div id="testContents">
                        <?php
                        for ($i = 0; $i < 15; $i++){
                            ?>
                            <div class="SelectedGroup">
                                <img src="img/drackbutton.png" style="vertical-align: middle; height: 32px; width: 32px; float: left;"/>
                                <img  src="img/delete.png" style="vertical-align: middle; height: 32px; width: 32px; float: right;"/>
                                <div class="miniselecttest">
                                    Име на избраната група със задачи:...
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </fieldset>
            </fieldset>
            <fieldset style="border-radius: 5px; border: 2px solid green;font-size: 16pt; height: 100%; background-color: rgb(0, 20, 0);"
                      data-dimensions="height: <#editTest>.offsetHeight - 24; width: <#editTestPage>.offsetWidth - <#editTest>.offsetWidth - 42;">
                <legend> Налични групи </legend>
                <div id="exerciseGroupsContainer">
                    <div class="contents">
                        <div class="block">
                            <div class="name" style="background-color: rgb(0, 10, 0);">
                                exercise group
                            </div>
                            <div class="description"  style="background-color: black;">
                                В разработка
                            </div>
                        </div>
                    </div>
                    <div class="newElementButton"></div>
                </div>
            </fieldset>
        </div>
        <div id="editTestPage" style="position: relative; display:none;">
            <fieldset id="editTest" style="background-color: rgb(0, 20, 0);">
                <legend> Група </legend>
                <fieldset class="textarea" style="background-color: rgb(0, 40, 0);">
                    <legend> Име </legend>
                    <div id="testName" contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea" style="margin-top: 5px; background-color: rgb(0, 40, 0);">
                    <legend> Описание </legend>
                    <div id="testDescription" contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea" style=" overflow-y: scroll;  margin-top: 5px; background-color: rgb(0, 40, 0); height: 300px;">
                    <legend> Задачи в групата </legend>
                    <div id="testContents">
                        <?php
                        for ($i = 0; $i < 15; $i++){
                            ?>
                            <div class="SelectedGroup">
                                <img src="img/drackbutton.png" style="vertical-align: middle; height: 32px; width: 32px; float: left;"/>
                                <img src="img/delete.png" style="vertical-align: middle; height: 32px; width: 32px; float: right;"/>
                                <div class="miniselecttest">
                                    Име на избраната задача:...
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </fieldset>
            </fieldset>
            <fieldset style="border-radius: 5px; border: 2px solid green;font-size: 16pt; height: 100%; background-color: rgb(0, 20, 0);"
                      data-dimensions="height: <#editTest>.offsetHeight - 24; width: <#editTestPage>.offsetWidth - <#editTest>.offsetWidth - 42;">
                <legend> Налични задачи </legend>
                <div id="exercisesContainer">
                    <div class="contents">
                        <div class="block">
                            <div class="name">
                                exercise
                            </div>
                            <div class="description">
                                В разработка
                            </div>
                        </div>
                    </div>
                    <div class="newElementButton"></div>
                </div>
            </fieldset>
        </div>
        <div id="editTestPage" style="position: relative; display:none;">
            <fieldset id="editTest" style="background-color: rgb(0, 20, 0);">
                <legend> Задача </legend>
                <fieldset class="textarea" style="background-color: rgb(0, 40, 0);">
                    <legend> Име </legend>
                    <div id="testName" contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea" style="margin-top: 5px; background-color: rgb(0, 40, 0);">
                    <legend> Описание </legend>
                    <div id="testDescription" contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea" style=" overflow-y: scroll;  margin-top: 5px; background-color: rgb(0, 40, 0); height: 300px;">
                    <legend> Формули или нещо такова </legend>
                </fieldset>
            </fieldset>
            <fieldset style="border-radius: 5px; border: 2px solid green;font-size: 16pt; height: 100%; background-color: rgb(0, 20, 0);"
                      data-dimensions="height: <#editTest>.offsetHeight - 24; width: <#editTestPage>.offsetWidth - <#editTest>.offsetWidth - 42;">
                <legend> Направи задача </legend>
            </fieldset>
        </div>
    </body>
</html>