<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>
            <?php echo $dictionary["title"]; ?>
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
                $sign_up = $dictionary["sign_up_messages"];
                $login = $dictionary["login_messages"];
                ?>
                <div class="dropdown_form_container" autocomplete="off">
                    <form action="./profile/sign-up" method="post" class="dropdown_form_contents">
                        <input placeholder="<?php echo $sign_up["name"]; ?>"
                               name="user" class="input" type="text" >
                        <input placeholder="<?php echo $sign_up["password"]; ?>"
                               name="pwd" class="input" type="password" >
                        <input placeholder="<?php echo $sign_up["repeat_password"]; ?>"
                               name="pwd2" class="input" type="password" >
                        <span class="dropdown_form_feedback"></span>
                    </form>
                    <div id="sign_up" class="dropdown_form_submit">
                        <?php echo $sign_up["sign_up"]; ?>
                    </div>
                </div>

                <div class="dropdown_form_container">
                    <form action="./profile/login" method="post" class="dropdown_form_contents">
                        <input placeholder="<?php echo $login["name"]; ?>"
                               name="user" class="input" type="text" >
                        <input placeholder="<?php echo $login["password"]; ?>"
                               name="pwd" class="input" type="password" >
                        <span class="dropdown_form_feedback"></span>
                    </form>
                    <div id="login" class="dropdown_form_submit">
                        <?php echo $login["login"]; ?>
                    </div>
                </div>
                <?php
                unset($sign_up, $login);
                ?>
            </div>
        </div>
        <div class="alignedTextContainer">
            <?php
            $contents = $dictionary["home_page_contents"];
            $counter = 0;
            foreach ($contents as $element){
                $counter++;
                $position = $element["position"] ?? $counter % 2 ? "right" : "left";
                if (is_array($element["content"])){
                    $element["content"] = implode(' ', $element["content"]);
                }
                $element["content"] = str_replace("\n", "<br />", $element["content"]);
                ?>

                <div class="element <?php echo $position; ?>">
                    <div class="topic">
                        <?php echo $element["topic"]; ?>
                    </div>
                    <br />
                    <div class="content"  style="min-width:500px;">
                        <?php echo $element["content"]; ?>
                        <div style="float: <?php echo $position; ?>; min-width:50px; min-height:10px;"></div>
                    </div>
                </div>

                <?php
            }
            unset($counter, $contents, $element, $position);
            ?>
        </div>
    </body>
</html>
