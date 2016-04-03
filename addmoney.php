<?php

require 'required.php';

$username = $VARS['username'];
$password = $VARS['password'];

require 'killOnUserPassError.php';

if (is_empty($VARS['merchant']) || is_empty($VARS['stripe']) || is_empty($VARS['amount']) || is_empty($VARS['type'])) {
    sendError("Missing required information.", true);
}

$balancetype = filter_var($VARS['type'], FILTER_SANITIZE_NUMBER_INT);

$merchant = filter_var($VARS['merchant'], FILTER_SANITIZE_NUMBER_INT);
\Stripe\Stripe::setApiKey($database->select('merchants', ['stripesk'], ['merchantid' => $merchant])[0]['stripesk']);

// Get the credit card details submitted by the form
$token = $VARS['stripe'];
$amount = filter_var($VARS['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

$realamount = (int) ((((float) $amount + 0.30) / (1.0 - 0.029)) * 100);

//file_put_contents('add_debug.txt', "amount: $amount\nreal amount: $realamount");
// Create the charge on Stripe's servers - this will charge the user's card
try {
    $userinfo = $database->select('users', ['email', 'realname', 'userid'], ['username' => $username])[0];
    $userid = $userinfo['userid'];
    $userrealname = $userinfo['realname'];
    $useremail = $userinfo['email'];
    $userprefs = $database->select('userprefs', '*', ['users_userid' => $userid])[0];
    $charge = \Stripe\Charge::create(array(
                "amount" => $realamount, // amount in cents
                "currency" => "usd",
                "source" => $token,
                "description" => "Top off account at " . $database->select('merchants', 'merchantmane', ['merchantid' => $merchant])[0]['merchantname'],
                "metadata" => array("customer_username" => $username)
    ));
    $balance = $database->select('balances', 'balance', ["AND" => ["users_userid" => $userid, "balancetypes_typeid" => $balancetype, "merchants_merchantid" => $merchant]])[0]['balance'];
    $newbalance = $balance + (float) $amount;
    
    $database->update('balances', ['balance[+]' => $amount], [
        "AND" => [
            'users_userid' => $userid,
            'balancetypes_typeid' => $balancetype,
            'merchants_merchantid' => $merchant
        ]
    ]);
    if ($userprefs['sendemailontransaction'] == 1) {
        $merchantname = $database->select('merchants', ['merchantname'], ['merchantid' => $merchant])[0]['merchantname'];
        $mail->addAddress($useremail, $userrealname);
        $mail->Subject = 'Funds Added';
        $dispamt = number_format((float) $amount, 2, '.', '');
        $body = "You added $$dispamt to your $merchantname account on " . date('l, F d, Y') . ' at ' . date('h:i:s a') . ".\r\nIf you did not approve this transaction, reply to this email with your concerns as soon as possible.";
        $footer = "You are receiving this email because you added funds to your $merchantname account.";
        $mail->msgHTML(
                str_replace(
                        "##FOOTER##", $footer, str_replace(
                                "##BODY##", "<p>" . str_replace(
                                        "\r\n", "</p><p>", $body
                                ) . "</p>", file_get_contents('emailtemplates/transaction-customer.inline.html')
                        )
                )
        );
        $mail->AltBody = "$body\r\n----------\r\n$footer";
        $mail->send();
    }
} catch (\Stripe\Error\Card $e) {
    // Since it's a decline, \Stripe\Error\Card will be caught
    $body = $e->getJsonBody();
    $err = $body['error'];
    sendError($err['message'], true);
} catch (\Stripe\Error\RateLimit $e) {
    sendError('Error: Try again in a few minutes.', true);
} catch (\Stripe\Error\InvalidRequest $e) {
    sendError('Error: Invalid request.', true);
} catch (\Stripe\Error\Authentication $e) {
    // Authentication with Stripe's API failed
    // (maybe you changed API keys recently)
    sendError('Error: Merchant server error.  Try again later.', true);
} catch (\Stripe\Error\ApiConnection $e) {
    // Network communication with Stripe failed
    sendError('Error: Cannot connect with processor.', true);
} catch (\Stripe\Error\Base $e) {
    // Display a very generic error to the user, and maybe send
    // yourself an email
    sendError('An error occurred.  Try again later.', true);
} catch (Exception $e) {
    // Something else happened, completely unrelated to Stripe
    sendError('Internal error occurred.  Try again later.', true);
}

sendOK();
