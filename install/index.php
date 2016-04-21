<?php
define("INSTALLER", true);
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <h1>NoidPay Server Setup</h1>
        <p>Welcome to NoidPay!  It looks like you're setting up a standalone server.</p>
        <?php
        switch ($_GET['step']) {
            case '2':
                include 'step2.php';
                break;
            case '3':
                include 'step3.php';
                break;
            case '4':
                include 'step4.php';
                break;
            case '1':
            default:
                include 'step1.php';
        }
        ?>
    </body>
</html>
