<?php

require 'required.php';

if ($VARS['secret'] !== 'awesomeadmin') {
    die("ACCESS DENIED.  GTFO.");
}
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

$userid = $database->insert('merchantlogins', [
        'merchants_merchantid' => $VARS['merchantid'],
        'username' => strtolower($VARS['username']),
        'password' => encryptPassword($VARS['password']),
        'realname' => $VARS['realname'],
        'email' => $VARS['email']
    ]);
sendOK($userid);