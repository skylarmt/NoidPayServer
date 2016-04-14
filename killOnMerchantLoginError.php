<?php
// Too lazy to be consistent
if (is_empty($VARS['merchantid'])) {
    $VARS['merchantid'] = $VARS['merchant'];
}

// Still empty, uh-oh
if (is_empty($VARS['merchantid'])) {
    sendError('No merchant specified.', true);
}

if (!$database->has('merchantlogins', ['AND' => ['username' => $VARS['username'], 'merchants_merchantid' => $VARS['merchantid']]])) {
    sendError('Invalid login.', true);
}

$userinfo = $database->select('merchantlogins', ['username', 'password'], ['merchants_merchantid' => $VARS['merchantid']])[0];
if (!comparePassword($VARS['password'], $userinfo['password'])) {
    sendError('Password incorrect.', true);
}