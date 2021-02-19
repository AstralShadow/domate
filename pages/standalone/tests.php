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

        <script defer src="./scripts/specific/TestsPageGUI/Container.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/exerciseContainer.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/groupsContainer.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/testsContainer.js"></script>

        <script defer src="./scripts/specific/TestsPageGUI/DefaultEditor.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/ContentListEditor.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/ExerciseEditor.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/editTest.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/editGroup.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/editExercise.js"></script>

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

        <!-- Test editor -->
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
                <fieldset class="textarea selectedElementsList"
                          data-dimensions="
                          height: windowHeight
                          - <#header>.offsetHeight
                          - <#testEditorPage>.offsetHeight
                          + <#testContents>.parentElement.offsetHeight - 20;"
                          >
                    <legend> Групи в теста </legend>
                    <div id="testContents"></div>
                </fieldset>
            </fieldset>
            <!-- Exercise-Groups container -->
            <fieldset class="editorContents"
                      data-dimensions="height: <#testDetails>.offsetHeight - 24;
                      width: <#testEditorPage>.offsetWidth - <#testDetails>.offsetWidth - 42;">
                <legend> Налични групи </legend>
                <div id="exerciseGroupsShadow" class="shadow"
                     data-dimensions="height: <#exerciseGroupsContainer>.offsetHeight;
                     width: <#exerciseGroupsContainer>.offsetWidth + 10;" >
                </div>
                <div id="exerciseGroupsContainer" class="container"></div>
            </fieldset>
        </div>
        <!-- Exercise-Group editor -->
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
                <fieldset class="textarea selectedElementsList"
                          data-dimensions="
                          height: windowHeight
                          - <#header>.offsetHeight
                          - <#groupEditorPage>.offsetHeight
                          + <#groupContents>.parentElement.offsetHeight - 20;"
                          >>
                    <legend> Задачи в групата </legend>
                    <div id="groupContents"></div>
                </fieldset>
            </fieldset>
            <!-- Exercises container -->
            <fieldset class="editorContents"
                      data-dimensions="height: <#groupDetails>.offsetHeight - 24;
                      width: <#groupEditorPage>.offsetWidth - <#groupDetails>.offsetWidth - 42;">
                <legend> Налични задачи </legend>
                <div id="exerciseGroupsShadow" class="shadow"
                     data-dimensions="height: <#exercisesContainer>.offsetHeight;
                     width: <#exercisesContainer>.offsetWidth + 10;" >
                </div>
                <div id="exercisesContainer" class="container"></div>
            </fieldset>
        </div>
        <!-- Exercise editor -->
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
                <fieldset class="textarea selectedElementsList"
                          data-dimensions="
                          height: windowHeight
                          - <#header>.offsetHeight
                          - <#exerciseExitorPage>.offsetHeight
                          + <#exerciseSideboard>.parentElement.offsetHeight - 20;"
                          >
                    <legend> Поле за селекция на формули тук. </legend>
                    <div id="exerciseSideboard">

                    </div>
                </fieldset>
            </fieldset>
            <!-- Exercise workspace -->
            <fieldset class="editorContents"
                      data-dimensions="height: <#exerciseDetails>.offsetHeight - 24;
                      width: <#exerciseExitorPage>.offsetWidth - <#exerciseDetails>.offsetWidth - 42;">
                <legend> Направи задача </legend>
                <div id="exerciseWorkspace">

                </div>
            </fieldset>
        </div>

        <!-- Tests container -->
        <div id="testsShadow" class="shadow"
             data-dimensions="height: <#testsContainer>.offsetHeight;
             width: <#testsContainer>.offsetWidth + 10;" >
        </div>
        <div id="testsContainer" class="container"
             data-dimensions="maxHeight: windowHeight - <#header>.offsetHeight - 10;" >
            <noscript>
            Разрешете JavaScript за правилното функциониране на сайта.
            </noscript>
        </div>

        <!-- GUI initalization -->
        <?php
        $TestsPageGUI_init = [
            "logDownloadTestData" => false,
            "logCreateCommands" => false,
            "animationSpeed" => 830
        ];
        $placeholders = $dictionary->contentPlaceholders;

        $keys = [
            "noTestName", "noTestDescription",
            "noGroupName", "noGroupDescription",
            "noExerciseName", "noExerciseDescription"
        ];
        foreach ($keys as $key){
            $TestsPageGUI_init[$key] = $placeholders[$key];
        }
        $json = json_encode($TestsPageGUI_init);

        echo "<script>var TestsPageGUI = " . $json . "</script>";

        unset($TestsPageGUI_init, $placeholders, $keys, $key, $json);
        ?>
    </body>
</html>