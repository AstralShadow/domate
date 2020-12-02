<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $dictionary->title; ?></title>

        <link href="stylesheets/main.css"
              rel="stylesheet" type="text/css">
        <link href="stylesheets/templates/default_main.css"
              rel="stylesheet" type="text/css">
        <link href="stylesheets/templates/default_navigation.css"
              rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php require "pages/templates/default_navigation.php"; ?>
        <?php include "pages/" . $page . ".php"; ?>
    </body>
</html>
