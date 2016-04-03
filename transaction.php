<?php

require 'required.php';

$username = $VARS['username'];
$password = $VARS['password'];

require 'killOnUserPassError.php';

$balancetype = filter_var($VARS['type'], FILTER_SANITIZE_NUMBER_INT);
$transID = filter_var($VARS['transid'], FILTER_SANITIZE_NUMBER_INT);

if (is_empty($balancetype) || is_empty($transID)) {
    die('Error: Missing required information.');
}

$userinfo = $database->select('users', ['email', 'realname', 'userid'], ['username' => $username])[0];
$userid = $userinfo['userid'];
$userrealname = $userinfo['realname'];
$useremail = $userinfo['email'];
$userprefs = $database->select('userprefs', '*', ['users_userid' => $userid])[0];
$transinfo = $database->select('transactions', ['merchantid', 'transamt', 'statuscode'], ['transid' => $transID])[0];
$amount = $transinfo['transamt'];
$merchantid = $transinfo['merchantid'];
$balance = $database->select('balances', ['balance'], ["AND" => ["users_userid" => $userid, "balancetypes_typeid" => $balancetype, "merchants_merchantid" => $merchantid]])[0]['balance'];
// Check if the transaction needs payment, avoid double-billing and stuff.
if ($transinfo['statuscode'] != 1) {
    $errormsg = "";
    switch ($transinfo['statuscode']) {
        case 0:
            $errormsg = "This transaction has already been completed.";
            break;
        case 2:
            $errormsg = "This transaction has been cancelled by the merchant.";
            break;
        case 3:
            $errormsg = "This transaction has been refunded.";
            break;
        default:
            $errormsg = "This transaction is invalid.  Try again with a new code.  (" . $transinfo['statuscode'] . ")";
    }
    sendError($errormsg, true);
}
if ($balance >= $amount) {
    $database->update('transactions', [
        'userid' => $userid,
        'balancetypes_typeid' => $balancetype,
        'statuscode' => 0,
        '#transcompletedate' => 'NOW()'
            ], [
        'transID' => $transID
    ]);
    $balance = (float) $balance - (float) $amount;
    $database->update('balances', ['balance' => $balance], [
        "AND" => [
            'users_userid' => $userid,
            'balancetypes_typeid' => $balancetype,
            'merchants_merchantid' => $merchantid
        ]
    ]);
    sendOK();

    // Send email to customer
    if ($usersendemail['sendemailontransaction'] == 1) {
        $merchantname = $database->select('merchants', ['merchantname'], ['merchantid' => $merchantid])[0]['merchantname'];
        $mail->addAddress($useremail, $userrealname);
        $mail->Subject = 'Transaction confirmation';
        $body = "You sent $amount to $merchantname on " . date('l, F d, Y') . ' at ' . date('h:i:s a') . ".\r\nIf you did not approve this transaction (#$transID), reply to this email with your concerns as soon as possible.";
        $footer = "You are receiving this email because you completed a transaction at $merchantname.";
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
} else {
    sendError('Declined: Insufficent funds in account.  Short by ' . ((float) $amount - (float) $balance), true);
}