<?php

require 'required.php';

require 'killOnMerchantLoginError.php';

$username = $VARS['customername'];
$merchant = filter_var($VARS['merchantid'], FILTER_SANITIZE_NUMBER_INT);
$balancetype = filter_var($VARS['type'], FILTER_SANITIZE_NUMBER_INT);

if (!isValidUserName($username)) {
    sendError('Invalid username.', true);
}

if (!username_exists($username)) {
    sleep(rand(1, 100) / 100);
    sendError('Username not found.', true);
}

if (!is_empty($merchant)) {
    $userid = $database->select('users', ['userid'], ['username' => $username])[0]['userid'];
    if (!$database->has('membership', ["AND" => ['users_userid' => $userid, 'merchants_merchantid' => $merchant]])) {
        sendError('This account exists, but is not linked to this merchant.', true);
    }
} else {
    sendError("Missing required merchant ID.", true);
}

if (is_empty($balancetype)) {
    sendError('Missing required information (type)', true);
}

$userid = $database->select('users', ['userid'], ['username' => $username])[0]['userid'];
$balances;
$balances = $database->select('balances', ['balance', 'balancetypes_typeid'], ["AND" =>
            ['users_userid' => $userid,
                'balancetypes_typeid' => $balancetype,
                'merchants_merchantid' => $merchant
            ]
        ])[0];

//var_dump($balances);
sendOK($balances['balance']);
