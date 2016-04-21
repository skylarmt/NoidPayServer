<?php if (!INSTALLER) {
    die('Access Denied.');
} ?>
<h2>Step 2: Merchant Info</h2>
<p>We need to setup a merchant in the database, so a bit of info is needed.</p>
<form action='index.php?step=3' method='post' style="border: 1px solid black;">
    <label for='merchantname'>Merchant Name:</label><br />
    <input type='text' name='merchantname' required='required' /><br />
    <label for='stripe'>Stripe secret key (so users can refill accounts with a card):</label><br />
    <input type='text' name='stripe' required='required' /><br />
    <p><i>Don't have a Stripe secret key?  Get a Stripe account at 
            <a href="https://stripe.com" target="_BLANK">https://stripe.com</a> 
            and come back with your secret key!</i>
    </p><br />
    <input type='submit' value='> Step 3 >' />
</form>