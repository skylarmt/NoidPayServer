<?php

/**
 * This script dies with errors if the vars $username and $password aren't in the database.
 */
if (!isValidUserName($username)) {
    sendError('Invalid username.', true);
}

if (!username_exists($username)) {
    sleep(rand(1, 100) / 100);
    sendError('Username not found.', true);
}

if (!authenticate_user($username, $password)) {
    sleep(rand(1, 100) / 100);
    sendError('Password incorrect.', true);
}

if (!is_empty($VARS['merchant'])) {
    $merchant = filter_var($VARS['merchant'], FILTER_SANITIZE_NUMBER_INT);
    $userid = $database->select('users', ['userid'], ['username' => $username])[0]['userid'];
    if (!$database->has('membership', ["AND" => ['users_userid' => $userid, 'merchants_merchantid' => $merchant]])) {
        sendError('This account exists, but is not linked to this merchant.  Please use the correct client app.', true);
    }
}