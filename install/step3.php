<?php if (!INSTALLER) {
    die('Access Denied.');
} ?>
<?php
require '../vendor/autoload.php';
require '../database_config.php';

$mid = $database->insert('merchants', ['merchantname' => $_POST['merchantname'], 'stripesk' => $_POST['stripe']]);
file_put_contents('merchantid.tmp', $mid);
echo "<p style='color: green;'>Merchant with ID #$mid created successfully!</p>";
?>
<h2>Step 3: Create User</h2>
<p>Now you need to setup a merchant login!</p>
<form action='index.php?step=4' method='post' style="border: 1px solid black;">
    <label for='username'>Username:</label><br />
    <input type='text' name='username' required='required' /><br />
    <label for='password'>Password:</label><br />
    <input type='text' name='password' required='required' /><br />
    <label for='realname'>Real name:</label><br />
    <input type='text' name='realname' required='required' /><br />
    <label for='email'>Email:</label><br />
    <input type='email' name='email' required='required' /><br />
    <input type='hidden' name='merchantid' value='<?php echo $mid; ?>' />
    <input type='submit' value='> Finish >' />
</form>