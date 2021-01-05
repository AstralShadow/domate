<!DOCTYPE html>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/secureTokens.php";
?>
<html>
    <head>
        <meta charset="utf-8" />
        <title>
            <?php echo $dictionary->title; ?>
        </title>

        <link href="./stylesheets/main.css"
              rel="stylesheet" type="text/css" />
        <link href="./stylesheets/home.css"
              rel="stylesheet" type="text/css" />
        <link href="./stylesheets/alignedText.css"
              rel="stylesheet" type="text/css" />

        <script async src="./scripts/visuals/dropdown_forms.js" ></script>
    </head>
    <body>
        <div id="home_header">
            <div id="home_logo">

            </div>
            <div id="home_forms">
                <?php
                $signUp = $dictionary->signUpMessages;
                $login = $dictionary->loginMessages;
                $signUpToken = generateSecureToken($session, "signUp");
                $loginToken = generateSecureToken($session, "login");
                ?>
                <div class="dropdown_form_container" autocomplete="off">
                    <form action="./?p=sign_up" method="post" class="dropdown_form_contents">
                        <input placeholder="<?php echo $signUp["name"]; ?>"
                               name="user" type="text" >
                        <input placeholder="<?php echo $signUp["password"]; ?>"
                               name="pwd" type="password" >
                        <input placeholder="<?php echo $signUp["repeatPassword"]; ?>"
                               name="pwd2" type="password" >
                        <span class="dropdown_form_feedback"></span>
                        <input name="token" type="hidden" value="<?php echo $signUpToken; ?>" />
                    </form>
                    <div id="sign_up" class="dropdown_form_submit">
                        <?php echo $signUp["signUp"]; ?>
                    </div>
                </div>

                <div class="dropdown_form_container">
                    <form action="./?p=login" method="post" class="dropdown_form_contents">
                        <input placeholder="<?php echo $login["name"]; ?>"
                               name="user" type="text" >
                        <input placeholder="<?php echo $login["password"]; ?>"
                               name="pwd" type="password" >
                        <span class="dropdown_form_feedback"></span>
                        <input name="token" type="hidden" value="<?php echo $loginToken; ?>" />
                    </form>
                    <div id="login" class="dropdown_form_submit">
                        <?php echo $login["login"]; ?>
                    </div>
                </div>
                <?php
                unset($signUp, $login, $signUpToken, $loginToken);
                ?>
            </div>
        </div>
        <div class="alignedTextContainer">
            <?php
            $contents = $dictionary->homePageContents;
            $counter = 0;
            foreach ($contents as $element){
                $counter++;
                $position = $element["position"] ?? $counter % 2 ? "right" : "left";
                ?>
                <div class="element <?php echo $position; ?>">
                    <div class="topic">
                        <?php echo $element["topic"]; ?>
                    </div>
                    <br />
                    <div class="content">
                        <?php echo $element["content"]; ?>
                    </div>
                </div>
                <?php
            }
            unset($counter, $contents, $element, $position);
            ?>
        </div>
    </body>
</html>
