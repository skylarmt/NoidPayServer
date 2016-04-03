<?php

require 'required.php';

$username = $VARS['username'];
$password = $VARS['password'];
$merchant = $VARS['merchant'];
$type = $VARS['type'];
if (is_empty($merchant) || is_empty($type)) {
    sendError("Required data not found.", true);
}

require 'killOnUserPassError.php';

$userid = $database->select('users', ['userid'], ['username' => $username])[0]['userid'];
$balances = $database->select('balances', ['balance', 'balancetypes_typeid'], [
            "AND" =>
            ['users_userid' => $userid, 'merchants_merchantid' => $merchant, "balancetypes_typeid" => $type]])[0];

if (JSON) {
    $balances['status'] = 'OK';
    echo json_encode($balances);
} else {
    echo "OK\n";
    echo $balances['balancetypes_typeid'] . '|' . $balances['balance'];
}