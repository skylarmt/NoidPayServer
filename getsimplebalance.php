<?php

require 'required.php';

$username = $VARS['username'];
$password = $VARS['password'];
$balancetype = $VARS['type'];

require 'killOnUserPassError.php';

$userid = $database->select('users', ['userid'], ['username' => $username])[0]['userid'];
$balances;
if (is_empty($VARS['merchant'])) {
    $balances = $database->select('balances', ['balance', 'balancetypes_typeid'], ["AND" =>
                ['users_userid' => $userid,
                    'balancetypes_typeid' => $balancetype]
            ])[0];
} else {
    $balances = $database->select('balances', ['balance', 'balancetypes_typeid'], ["AND" =>
                ['users_userid' => $userid,
                    'balancetypes_typeid' => $balancetype,
                    'merchants_merchantid' => $VARS['merchant']
                ]
            ])[0];
}

//var_dump($balances);
sendOK($balances['balance']);
