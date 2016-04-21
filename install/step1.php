<?php
if (!INSTALLER) {
    die('Access Denied.');
}
?>
<h2>Step 1: Self-test</h2>
<pre><?php
    $testfailed = false;
    echo "\nStart of self test\nAt this point, it should be clear if PHP is working :)\n\n";
    if (!is_writable("../")) {
        echo "Warning: PHP doesn't have permission to change files.\n  This means you'll have to disable this installer tool after use manually.\n";
    }
    try {
        echo "Loading Composer...\n";
        require '../vendor/autoload.php';
        echo "Composer loaded OK!\n";
    } catch (Exception $ex) {
        $testfailed = true;
        echo "An error occurred while loading Composer.\n";
    }
    try {
        echo "Connecting to database...\n";
        require '../database_config.php';
        echo "Database connected OK!\n";
    } catch (Exception $ex) {
        $testfailed = true;
        echo "An error occurred while connecting to the database.\n  Please make sure the database_config.php file\n  is present and has the correct information.\n";
    }
    echo "\nEnd of self test.\n";
    ?></pre>
<p><?php
    if ($testfailed) {
        echo "It looks like there was a problem or two.  Go fix it, and come back here when you're done.</p></body></html>";
        die();
    } else {
        echo "That looks great!  On to step 2!";
    }
    ?>
</p>
<form action='index.php' method='get'>
    <input type='hidden' name='step' value='2' />
    <input type='submit' value='> Step 2 >' />
</form>