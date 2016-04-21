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
        <h1>NoidPay Server</h1>
        <pre>
            <?php
            echo "Start of self test\nAt this point, it should be clear if PHP is working :)\n\n";
            try {
                echo "Loading Composer...\n";
                require 'vendor/autoload.php';
                echo "Composer loaded OK!\n";
            } catch (Exception $ex) {
                echo "An error occurred while loading Composer.\n";
            }
            try {
                echo "Connecting to database...\n";
                require 'database_config.php';
                echo "Database connected OK!\n";
            } catch (Exception $ex) {
                echo "An error occurred while connecting to the database.\n  Please make sure the database_config.php file\n  is present and has the correct information.\n";
            }
            echo "\nEnd of self test.\n";
            ?>
        </pre>
    </body>
</html>
