<?php

require 'required.php';

if (!isValidUserInfo($VARS['username'], $VARS['email'])) {
    sendError('Invalid username or email.', true);
}
if (is_empty($VARS['password'])) {
    sendError("A password is required.", true);
}
if (is_empty($VARS['realname'])) {
    sendError("Please enter your real name.  We're not evil, don't worry!", true);
}
if (is_empty($VARS['merchantid'])) {
    sendError('Incomplete data.', true);
}
if (is_empty($VARS['initbalance'])) {
    $VARS['initbalance'] = 0;
}
if (username_exists($VARS['username'])) {
    sendError('Username taken.  Try another.', true);
}
$userid = adduser($VARS['username'], $VARS['password'], $VARS['realname'], $VARS['email']);

$database->insert('balances', ['users_userid' => $userid, 'balancetypes_typeid' => 1, 'balance' => $VARS['initbalance'], 'merchants_merchantid' => $VARS['merchantid']]);
$database->insert('membership', ['users_userid' => $userid, 'merchants_merchantid' => $VARS['merchantid']]);
sendOK($userid);