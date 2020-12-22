<!DOCTYPE html>
<html>
    <head>
        <link href="./stylesheets/main.css"
              rel="stylesheet" type="text/css" />
        <link href="./stylesheets/home.css"
              rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div id="home_header">
            <div id="home_logo">

            </div>
            <div id="home_forms">
                <?php
                $sign_up = $dictionary->sign_up_placeholders;
                $login = $dictionary->login_placeholders;
                ?>
                <div class="dropdown_form_container">
                    <form method="post">
                        <input placeholder="<?php echo $sign_up["name"]; ?>"
                               name="user" type="text" />
                        <br/>
                        <input placeholder="<?php echo $sign_up["password"]; ?>"
                               name="pass" type="password" />
                        <br/>
                        <input placeholder="<?php echo $sign_up["repeat_password"]; ?>"
                               name="pass2" type="password" />
                    </form>
                    <div id="sign_up" class="dropdown_form_submit">
                        <?php echo $sign_up["sign_up"]; ?>
                    </div>
                </div>

                <div class="dropdown_form_container">
                    <form method="post">
                        <input placeholder="<?php echo $login["name"]; ?>"
                               name="user" type="text" />
                        <br/>
                        <input placeholder="<?php echo $login["password"]; ?>"
                               name="pass" type="password" />
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
        <div id="home_container">
            <?php
            $contents = $dictionary->home_page_contents;
            $counter = 0;
            foreach ($contents as $element){
                $counter++;
                if (isset($element["position"]))
                    $position = $element["position"];
                else
                    $position = $counter % 2 ? "right" : "left";
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
            ?>
        </div>
    </body>
</html>
