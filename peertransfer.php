<?php
/**
 * Send money from a user's balance to a different user's balance, gift style.
 * 
 * Requires the following POST variables:
 * 
 * username (username)
 * password (password)
 * type (balance type)
 * merchant (merchant ID)
 * amt (transaction amount)
 * sendto (receipient username
 */


require 'required.php';

$username = $VARS['username'];
$password = $VARS['password'];

require 'killOnUserPassError.php';

$balancetype = filter_var($VARS['type'], FILTER_SANITIZE_NUMBER_INT);
$recuser = $VARS['sendto'];
$merchant = filter_var($VARS['merchant'], FILTER_SANITIZE_NUMBER_INT);
$amount = filter_var($VARS['amt'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

if (is_empty($balancetype) || is_empty($recuser) || is_empty($amount) || is_empty($VARS['amt']) || is_empty($merchant)) {
    sendError("Missing required information.", true);
}

if (!isValidUserName($recuser)) {
    sendError('The receipient username is invalid.', true);
}

if (!username_exists($recuser)) {
    sendError("The receipient does not exist.", true);
}

$recuserid = $database->select('users', ['userid'], ['username' => $recuser])[0]['userid'];
if (!$database->has('membership', ["AND" => ['users_userid' => $recuserid, 'merchants_merchantid' => $merchant]])) {
    sendError('The receipient does not have an account with this merchant.', true);
}

$balance = $database->select('balances', ['balance'], ["AND" => ["users_userid" => $userid, "balancetypes_typeid" => $balancetype, "merchants_merchantid" => $merchant]])[0]['balance'];

if ($balance >= $amount) {
    $balance = (float) $balance - (float) $amount;
    $database->update('balances', ['balance' => $balance], [
        "AND" => [
            'users_userid' => $userid,
            'balancetypes_typeid' => $balancetype,
            'merchants_merchantid' => $merchant
        ]
    ]);
    $orecbalance = $database->select('balances', ['balance'], ["AND" => ["users_userid" => $recuserid, "balancetypes_typeid" => $balancetype, "merchants_merchantid" => $merchant]])[0]['balance'];
    $recbalance = (float) $orecbalance + (float) $amount;
    $database->update('balances', ['balance' => $recbalance], [
        "AND" => [
            'users_userid' => $recuserid,
            'balancetypes_typeid' => $balancetype,
            'merchants_merchantid' => $merchant
        ]
    ]);
    sendOK();
} else {
    sendError('Declined: Insufficent funds in account.  Short by ' . ((float) $amount - (float) $balance), true);
}