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

$userid = $database->select('users', ['userid'], ['username' => $username])[0]['userid'];
$usersendemail = $database->select('userprefs', ['sendemailontransaction'], ['users_userid' => $userid])[0];
$amount = $database->select('transactions', ['transamt'], ['transid' => $transID])[0]['transamt'];
$balance = $database->select('balances', ['balance'], ["AND" => ["users_userid" => $userid, "balancetypes_typeid" => $balancetype]])[0]['balance'];
var_dump($balance, $amount, $userid, $balancetype);