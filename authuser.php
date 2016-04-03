<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'required.php';

$username = $VARS['username'];
$password = $VARS['password'];
$merchant = $VARS['merchant'];

require 'killOnUserPassError.php';

sendOK();