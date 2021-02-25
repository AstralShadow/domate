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

        <script src="./scripts/mathjaxConfig.js"></script>
        <script async src="./mathjax/startup.js" id="MathJax-script"></script>

    </head>
    <body class="nomathjax">
        <div id="header">
            <div id="logo"></div>
            <div class="alignedTextContainer"
                 data-dimensions="width: <#header>.offsetWidth - <#logo>.offsetWidth - 75;">
                <div class="element left">
                    <div class="topic">
                        Здравейте, <?php echo $user->user; ?>
                        <!---Как да създадем тест?-->
                    </div>
                    <br />
                    <div class="content">
                        <div style="float: right; min-width:50px; min-height:10px;"></div>
                        Тук можете да създавате, редактирате, 
                        стартирате и оценявате тестове.    
                        За да се получат различни варианти за всеки
                        ученик е необхгодимо да въведете колекция от
                        въпроси.<br />
                        Още инструкции можете да намерите тук.
                        <!---Тук виждате създадените от Вас тестове.
                        Във всеки тест виждате групите задачи от който
                        се състои, а във всяка граупа можете да видите 
                        задачите от който се състои. Като изберете даден
                        тест можете да редактирате съдържанието, 
                        изтриете или пуснете теста на Вашите ученици. 
                        Като изберете дадена група задачи можете да 
                        редактирате съдържанието, изтриете или 
                        добавите към избрания тест. Като изберете 
                        дадена задача можете да 
                        редактирате условието, изтриете или 
                        добавите към избраната група. -->
                        <div style="float:left; min-width:50px; min-height:10px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="notification">
            Сигурни ли сте, че искате да изтриете това?
            <p/> Ако го изтриете повече няма да можете да го възстановите.
            <div class="buttonNotification" style="float: right;">
                Отказ
            </div>
            <div class="buttonNotification" style="float: left;">
                Изтрии
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
                          >
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
                <fieldset class="textarea">
                    <legend> Настройки на задача </legend>
                    <div id="exerciseSettings">
                        <fieldset class="exerciseConfig">
                            <legend>
                                <input type="checkbox" class="configCheckbox" />
                                Отговор
                            </legend>
                            <div id="exerciseAnswer"
                                 contentEditable="true"></div>
                        </fieldset>
                    </div>
                </fieldset>
                <fieldset class="textarea selectedElementsList"
                          data-dimensions="
                          height: windowHeight
                          - <#header>.offsetHeight
                          - <#exerciseExitorPage>.offsetHeight
                          + <#exerciseSideboard>.parentElement.offsetHeight - 20;"
                          >
                    <legend> Формули </legend>
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
                    <div id="exerciseDisplay" class="mathjax"></div>
                    <div id="exerciseInput" contentEditable="true">
                        `x=(-b +- sqrt(b^2 - 4ac))/(2a)`
                    </div>
                </div>
            </fieldset>
        </div>
        <div id="startTest" class="notification" >
            <span style="font-size:16pt;" id="ST_name"></span><br />
            <span style="font-size:12pt;">
                <span id="ST_description"></span><br />
                (<span id="ST_tasks">0</span> задачи)<br /><br />
            </span>
            <label>
                Достъпен от:
                <input name="start" type="datetime-local" class="input" >
            </label>
            <label>
                Достъпен до:
                <input name="end" type="datetime-local" class="input" >
            </label>
            <label>
                Минути за работа:
                <input name="worktime" min="1" type="number" class="input">
            </label>
            <fieldset class="textarea" style="padding: 5px; margin: 5px; width:auto;">
                <legend>
                    Бележка
                </legend>
                <div style="min-height: 40px;"
                     contenteditable="true" id="ST_note">
                </div>
            </fieldset>
            <div class="input" id="ST_button" style="width: auto; margin: 10px;">
                Насрочи
            </div>
            <span id="ST_feedback"></span>
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