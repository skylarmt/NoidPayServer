<?php

require 'required.php';

if ($_GET['get'] == '1') {
    $VARS = $_GET;
}

$transid = $VARS['transid'];
$data = $database->select('transactions', ['userid', 'transamt', 'statuscode'], ['transid' => $transid])[0];
if (JSON) {
    $data['status'] = 'OK';
    die(json_encode($data));
} else {
    die('OK:' . $data['statuscode'] . '|' . $data['transamt'] . '|' . $data['userid']);
}