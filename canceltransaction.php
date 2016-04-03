<?php

require 'required.php';

if ($_GET['get'] == '1') {
    $VARS = $_GET;
}

$transid = filter_var($VARS['transid'], FILTER_SANITIZE_NUMBER_INT);
$merchant = filter_var($VARS['merchant'], FILTER_SANITIZE_NUMBER_INT);

if (is_empty($VARS['transid']) || is_empty($VARS['merchant'])) {
    die('Error: Missing required information.');
}

$database->update('transactions', ['statuscode' => '2'], ['transid' => $transid]);

sendOK();