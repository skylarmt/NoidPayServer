<?php

require 'required.php';

$VARS = $_GET;

if (is_empty($VARS['merchantid'])) {
    sendError("Query requires merchant information.", true);
}
if ($VARS['all'] == '1') {
    $deals = $database->select('deals', [
        'dealid',
        'dealtitle',
        'dealhasurl',
        'dealurl',
        'dealhtml',
        'validafter',
        'validbefore'
            ], [
        'merchantid' => $VARS['merchantid']
    ]);
} else {
    $deals = $database->select('deals', [
        'dealid',
        'dealtitle',
        'dealhasurl',
        'dealurl',
        'dealhtml',
        'validafter',
        'validbefore'
            ], [
        "AND" => [
            '#validafter[<]' => 'NOW()',
            '#validbefore[>]' => 'NOW()',
            'merchantid' => $VARS['merchantid']
        ]
    ]);
}
$output['status'] = 'OK';
$output['deals'] = $deals;
echo json_encode($output);
