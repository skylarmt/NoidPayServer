<?php
require 'required.php';
include('phpqrcode/qrlib.php');
$VARS = $_GET;

// text output
$transid = $VARS['transid'];
$data = $database->select('transactions', ['transamt', 'merchantid'], ['transid' => $transid])[0];
$codeContents = 'http://noidpay.net/#' . $data['transamt'] . "|" . $transid;

// generating
QRcode::png($codeContents, false, QR_ECLEVEL_H, 10, 2);
