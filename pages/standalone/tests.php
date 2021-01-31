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

    </head>
    <body>
        <div id="nachalo">
            <div id="logo">

            </div>
            <div class="alignedTextContainer" data-dimensions="style.width: <#nachalo>.offsetWidth - <#logo>.offsetWidth - 75;">
                <div class="element left">
                    <div class="topic" data-dimensions="style.maxWidth: <#description>.offsetWidth - 200;">
                        Как се създава тест? (или н.т.)
                    </div>
                    <br />
                    <div class="content" id="description" style="position:relative;">
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
        <div id="menu">
            <div id="test">
                <div id="testname">
                    Заглавие:
                </div>
                <div id="opisanie">
                    Описание:
                </div>
            </div>
            <div id="test">
                <div id="testname">
                    Заглавие:
                </div>
                <div id="opisanie">
                    Описание:
                </div>
            </div>
            <div id="test">
                <div id="testname">
                    Заглавие:
                </div>
                <div id="opisanie">
                    Описание:
                </div>
            </div>
            <div id="test">
                <div id="testname">
                    Заглавие:
                </div>
                <div id="opisanie">
                    Описание:
                </div>
            </div>
            <div id="test">
                <div id="testname">
                    Заглавие:
                </div>
                <div id="opisanie">
                    Описание:
                </div>
            </div>
            <div id="test">
                <div id="testname">
                    Заглавие:
                </div>
                <div id="opisanie">
                    Описание:
                </div>
            </div>
            <div id="test">
                <div id="testname">
                    Заглавие:
                </div>
                <div id="opisanie">
                    Описание:
                </div>
            </div>
            <div id="test">
                <div id="testname">
                    Заглавие:
                </div>
                <div id="opisanie">
                    Описание:
                </div>
            </div>
            <div id="test">
                <div id="testname">
                    Създай:
                </div>
                <div id="opisanie">
                    +
                </div>
            </div>
        </menu>
</body>
</html>