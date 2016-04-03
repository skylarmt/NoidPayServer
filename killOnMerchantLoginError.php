<?php
if (!$database->has('merchantlogins', ['AND' => ['username' => $VARS['username'], 'merchants_merchantid' => $VARS['merchantid']]])) {
    sendError('Invalid login.', true);
}

$userinfo = $database->select('merchantlogins', ['username', 'password'], ['merchants_merchantid' => $VARS['merchantid']])[0];
if (!comparePassword($VARS['password'], $userinfo['password'])) {
    sendError('Password incorrect.', true);
}