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
            <?php echo $dictionary["title"]; ?>
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

        <script defer src="./scripts/specific/TestsPageGUI/HelperMenu.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/CloseButton.js"></script>
        <script defer src="./scripts/specific/TestsPageGUI/ResultsManager.js"></script>

        <script src="./scripts/mathjaxConfig.js"></script>
        <script async src="./mathjax/startup.js" id="MathJax-script"></script>

    </head>
    <body class="nomathjax">
        <!-- Header -->
        <div id="header">
            <?php
            /* Instructions */
            $instructions = [];
            foreach ($dictionary["tests_page_instructions"] as $key => $value){
                while (is_array($value) && count($value) > 2){
                    $c = count($value);
                    $value[$c - 2] = $value[$c - 2] . $value[$c - 1];
                    unset($value[$c - 1]);
                }
                $instructions[$key] = $value;
            }
            ?>
            <div id="logo">
                <div id="end" onclick="logout()">
                    Изход
                </div>
                <script>
                    function logout () {
                        var request = new XMLHttpRequest()
                        request.addEventListener("load", function () {
                            location.reload()
                        })
                        request.open("get", "./profile/logout")
                        request.send()
                    }
                </script>
            </div>
            <div class="alignedTextContainer"
                 data-dimensions="width: <#header>.offsetWidth - <#logo>.offsetWidth - 75;">
                <div class="element left" style="margin-bottom: -30px;">
                    <div class="topic">
                        <?php echo $instructions["main"][0]; ?>
                    </div>
                    <br />
                    <div class="content">
                        <div style="float: right; min-width:50px; min-height:10px;"></div>
                        <span id="description">
                            <?php echo $instructions["main"][1]; ?>
                        </span>
                        <div style="float:left; min-width:50px; min-height:10px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete notification -->
        <div class="notification" id="delNotification">
            <?php echo str_replace("\n", "<br />", $dictionary["delete_confirm"]); ?>
            <div id="noDelButton" class="buttonNotification" style="float: right;">
                <?php echo $dictionary["cancel"]; ?>
            </div>
            <div id="delButton" class="buttonNotification" style="float: left;">
                <?php echo $dictionary["delete"]; ?>
            </div>
        </div>
        <!-- Exams list -->
        <div id="examListContainer"></div>
        <!-- Exam results -->
        <div id="singleExamResults">
            <table id="scoreboardTable">
                <thead>
                    <tr class="tr">
                        <?php
                        echo <<<EOF
                        <th class="th" id="identificationQuestion">
                            $dictionary->name:
                        </th>
                        <th class="th"> $dictionary->started_at: </th>
                        <th class="th"> $dictionary->worked_for: </th>
                        <th class="th"> $dictionary->tasks:  </th>
                        <th class="th"> $dictionary->correct_tasks: </th>
EOF;
                        ?>
                    </tr>
                </thead>
                <tbody> </tbody>
            </table>
        </div>
        <!-- Task results -->
        <div id="taskCheck">
            <?php
            echo <<<EOL
            <span id="studentIdentification">
                $dictionary->name:
            </span>
            <hr />
            
                $dictionary->condition:
            <hr />
            
            <div id="taskQuestion"></div>
            <hr />
            
            <div id="studentAnswer">
                $dictionary->answer (??:??):
            </div>
            <br/>
            <hr />
            
            <div id="goodAnswer" class="button button_green" style="float: left; ">
                $dictionary->true
            </div>
            <div id="badAnswer" class="button button_red" style="float: right; ">
                $dictionary->wrong
            </div>
EOL;
            ?>
        </div>
        <!-- Test editor -->
        <div id="testEditorPage" class="page">
            <div class="pageCloseButton"></div>
            <fieldset id="testDetails" class="details">
                <legend> <?php echo $dictionary["exam"]; ?> </legend>
                <fieldset class="textarea">
                    <legend> <?php echo $dictionary["name"]; ?> </legend>
                    <div id="testName" class="title"
                         contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea">
                    <legend> <?php echo $dictionary["description"]; ?> </legend>
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
                    <legend> <?php echo $dictionary["groups_in_exam"]; ?> </legend>
                    <div id="testContents"></div>
                </fieldset>
            </fieldset>
            <!-- Exercise-Groups container -->
            <fieldset class="editorContents"
                      data-dimensions="height: <#testDetails>.offsetHeight - 24;
                      width: <#testEditorPage>.offsetWidth - <#testDetails>.offsetWidth - 42;">
                <legend> <?php echo $dictionary["avaliable_groups"]; ?> </legend>
                <div id="exerciseGroupsShadow" class="shadow"
                     data-dimensions="height: <#exerciseGroupsContainer>.offsetHeight;
                     width: <#exerciseGroupsContainer>.offsetWidth + 10;" >
                </div>
                <div id="exerciseGroupsContainer" class="container"></div>
            </fieldset>
        </div>
        <!-- Exercise-Group editor -->
        <div id="groupEditorPage" class="page">
            <div class="pageCloseButton"></div>
            <fieldset id="groupDetails" class="details">
                <legend> <?php echo $dictionary["group"]; ?> </legend>
                <fieldset class="textarea">
                    <legend> <?php echo $dictionary["name"]; ?> </legend>
                    <div id="groupName" class="title"
                         contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea">
                    <legend> <?php echo $dictionary["description"]; ?> </legend>
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
                    <legend> <?php echo $dictionary["exercises_in_group"]; ?> </legend>
                    <div id="groupContents"></div>
                </fieldset>
            </fieldset>
            <!-- Exercises container -->
            <fieldset class="editorContents"
                      data-dimensions="height: <#groupDetails>.offsetHeight - 24;
                      width: <#groupEditorPage>.offsetWidth - <#groupDetails>.offsetWidth - 42;">
                <legend> <?php echo $dictionary["avaliable_exercises"]; ?> </legend>
                <div id="exerciseGroupsShadow" class="shadow"
                     data-dimensions="height: <#exercisesContainer>.offsetHeight;
                     width: <#exercisesContainer>.offsetWidth + 10;" >
                </div>
                <div id="exercisesContainer" class="container"></div>
            </fieldset>
        </div>
        <!-- Exercise editor -->
        <div id="exerciseExitorPage" class="page" >
            <div class="pageCloseButton"></div>
            <fieldset id="exerciseDetails" class="details">
                <legend> <?php echo $dictionary["exercise"]; ?> </legend>
                <fieldset class="textarea">
                    <legend> <?php echo $dictionary["name"]; ?> </legend>
                    <div id="exerciseName" class="title"
                         contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea">
                    <legend> <?php echo $dictionary["description"]; ?> </legend>
                    <div id="exerciseDescription" class="description"
                         contentEditable="true"></div>
                </fieldset>
                <fieldset class="textarea">
                    <legend> <?php echo $dictionary["exercise_settings"]; ?> </legend>
                    <div id="exerciseSettings">
                        <fieldset class="exerciseConfig">
                            <legend>
                                <input type="checkbox" class="configCheckbox" />
                                <?php echo $dictionary["answer"]; ?>
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
                    <legend> <?php echo $dictionary["formulas"]; ?> </legend>
                    <div id="exerciseSideboard">

                    </div>
                </fieldset>
            </fieldset>
            <!-- Exercise workspace -->
            <fieldset class="editorContents"
                      data-dimensions="height: <#exerciseDetails>.offsetHeight - 24;
                      width: <#exerciseExitorPage>.offsetWidth - <#exerciseDetails>.offsetWidth - 42;">
                <legend> <?php echo $dictionary["exercise_workspace_legend"]; ?> </legend>
                <div id="exerciseWorkspace">
                    <div id="exerciseDisplay" class="mathjax"></div>
                    <div id="exerciseInput" contentEditable="true">
                        `x=(-b +- sqrt(b^2 - 4ac))/(2a)`
                    </div>
                </div>
            </fieldset>
        </div>
        <div id="startTest" class="notification" >
            <div class="pageCloseButton"></div>
            <span style="font-size:16pt;" id="ST_name"></span><br />
            <span style="font-size:12pt;">
                <span id="ST_description"></span><br />
                (<span id="ST_tasks">0</span> <?php echo $dictionary["exercises"]; ?>)<br />
            </span>
            <label>
                <fieldset class="textarea" style="margin:5px; padding: 0px; width:auto;">
                    <legend>
                        <?php echo $dictionary["starting_at"]; ?>:
                    </legend>
                    <input name="start" style="border: 0px solid black;" type="datetime-local" class="input" >
                </fieldset>
            </label>
            <label>
                <fieldset class="textarea" style="margin:5px; padding: 0px; width:auto;">
                    <legend>
                        <?php echo $dictionary["ending_at"]; ?>:
                    </legend>
                    <input name="end" style="border: 0px solid black;" type="datetime-local" class="input" >
                </fieldset>
            </label>
            <label>
                <fieldset class="textarea" style="margin:5px; padding: 0px; width:auto;">
                    <legend>
                        <?php echo $dictionary["worktime_in_minutes"]; ?>:
                    </legend>
                    <input name="worktime" style="border: 0px solid black;" min="1" type="number" class="input">
                </fieldset>
            </label>
            <label>
                <fieldset class="textarea" style="padding: 5px; margin: 5px; width:auto;">
                    <legend>
                        <?php echo $dictionary["identification_question"]; ?>:
                    </legend>
                    <input id="ST_identification" style="border: 0px solid black;"
                           type="text" class="input" value="Име на ученик:">
                </fieldset>
            </label>
            <label>
                <fieldset class="textarea" style="padding: 5px; margin: 5px; width:auto;">
                    <legend>
                        <?php echo $dictionary["note_to_self"]; ?>
                    </legend>
                    <input id="ST_note" style="border: 0px solid black;"
                           type="text" class="input" placeholder="Няма бележка">
                </fieldset>
            </label>
            <div class="input" id="ST_button" style="width: auto; margin: 10px; cursor: pointer;">
                <?php echo $dictionary["schedule"]; ?>
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
            <?php echo $dictionary["you_need_javascript"]; ?>
            </noscript>
        </div>
        <!-- GUI initalization -->
        <?php
        $TestsPageGUI_init = [
            "log_download_test_data" => false,
            "log_create_commands" => false,
            "animation_speed" => 830
        ];
        $placeholders = $dictionary["content_placeholders"];

        /* Placeholders */
        $keys = [
            "no_test_name", "no_test_description",
            "no_group_name", "no_group_description",
            "no_exercise_name", "no_exercise_description"
        ];
        foreach ($keys as $key){
            $TestsPageGUI_init[$key] = $placeholders[$key];
        }

        $TestsPageGUI_init["instructions"] = $instructions;

        $json = json_encode($TestsPageGUI_init);

        echo "<script>var TestsPageGUI = " . $json . "</script>";

        unset($TestsPageGUI_init, $placeholders, $keys, $key, $json);
        ?>
    </body>
</html>