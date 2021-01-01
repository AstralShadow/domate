<!DOCTYPE html>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require "include/functions/generateSecureToken.php";
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

        <script async src="./scripts/visuals/dropdown_forms.js" ></script>
    </head>
    <body>
        <div id="home_header">
            <div id="home_logo">

            </div>
            <div id="home_forms">
                <?php
                $sign_up = $dictionary->sign_up_messages;
                $login = $dictionary->login_messages;
                $sign_up_token = generateSecureToken($session, "sign_up");
                $login_token = generateSecureToken($session, "login");
                ?>
                <div class="dropdown_form_container" autocomplete="off">
                    <form action="./?p=sign_up" method="post" class="dropdown_form_contents">
                        <input placeholder="<?php echo $sign_up["name"]; ?>"
                               name="user" type="text" >
                        <input placeholder="<?php echo $sign_up["password"]; ?>"
                               name="pwd" type="password" >
                        <input placeholder="<?php echo $sign_up["repeat_password"]; ?>"
                               name="pwd2" type="password" >
                        <span class="dropdown_form_feedback"></span>
                        <input name="token" type="hidden" value="<?php echo $sign_up_token; ?>" />
                    </form>
                    <div id="sign_up" class="dropdown_form_submit">
                        <?php echo $sign_up["sign_up"]; ?>
                    </div>
                </div>

                <div class="dropdown_form_container">
                    <form action="./?p=login" method="post" class="dropdown_form_contents">
                        <input placeholder="<?php echo $login["name"]; ?>"
                               name="user" type="text" >
                        <input placeholder="<?php echo $login["password"]; ?>"
                               name="pwd" type="password" >
                        <span class="dropdown_form_feedback"></span>
                        <input name="token" type="hidden" value="<?php echo $login_token; ?>" />
                    </form>
                    <div id="login" class="dropdown_form_submit">
                        <?php echo $login["login"]; ?>
                    </div>
                </div>
                <?php
                unset($sign_up, $login, $sign_up_token, $login_token);
                ?>
            </div>
        </div>
        <div id="home_container">
            <?php
            $contents = $dictionary->home_page_contents;
            $counter = 0;
            foreach ($contents as $element){
                $counter++;
                $position = $element["position"] ?? $counter % 2 ? "right" : "left";
                ?>
                <div class="element <?php echo $position; ?>">
                    <div class="topic">
                        <?php echo $element["topic"]; ?>
                    </div>
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
