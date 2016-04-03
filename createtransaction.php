<?php

require 'required.php';

if ($_GET['get'] == '1') {
    $VARS = $_GET;
}

$amount = filter_var($VARS['amt'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$merchant = filter_var($VARS['merchant'], FILTER_SANITIZE_NUMBER_INT);

if (is_empty($VARS['amt']) || is_empty($VARS['merchant'])) {
    die('Error: Missing required information.');
}

$transid = $database->insert('transactions', ['merchantid' => $merchant, 'transamt' => $amount, 'statuscode' => 1, '#transdate' => "NOW()"]);

sendOK($transid, true);