<?php

require 'required.php';
require 'killOnMerchantLoginError.php';


$dealid = (is_empty($VARS['dealid']) ? -1 : $VARS['dealid']);


if ($VARS['action'] == 'get') {
    if (is_empty($VARS['dealid'])) {
        sendError("Missing required dealid.", true);
    }
    $deal = $database->select('deals', [
                'dealid',
                'dealtitle',
                'dealhtml',
                'validafter',
                'validbefore'
                    ], [
                'AND' => [
                    'dealid' => $dealid,
                    'merchantid' => $VARS['merchantid']
                ]
            ])[0];
    $deal['status'] = 'OK';
    $deal['validafter'] = date('Y-m-d', strtotime($deal['validafter']));
    $deal['validbefore'] = date('Y-m-d', strtotime($deal['validbefore']));
    echo json_encode($deal);
} else if ($VARS['action'] == 'delete') {
    if (is_empty($dealid)) {
        sendError("Missing required dealid.", true);
    }
    $database->delete('deals', ['AND' => ['merchantid' => $VARS['merchantid'], 'dealid' => $dealid]]);
    sendOK('', true);
} else if ($VARS['action'] == 'edit' || $VARS['action'] == 'new') {
    // Check if everything is here
    if (is_empty($VARS['title'])) {
        sendError('Missing title!', true);
    }
    if (is_empty($VARS['content'])) {
        sendError('Missing content!', true);
    }
    if (is_empty($VARS['validafter'])) {
        sendError('Missing start date!', true);
    }
    if (is_empty($VARS['validbefore'])) {
        sendError('Missing expiration date!', true);
    }

    // Sanitize and validate dates
    $vbf = date("Y-m-d H:i:s", strtotime($VARS['validbefore']));
    $vaf = date("Y-m-d H:i:s", strtotime($VARS['validafter']));
    if (!$vaf) {
        sendError('Invalid start date format!', true);
    }
    if (!$vbf) {
        sendError('Invalid expiration date format!', true);
    }

    // Everything seems legit by now
    if ($VARS['action'] == 'edit') {
        $database->update(
                'deals', [
            'merchantid' => $VARS['merchantid'],
            'dealtitle' => $VARS['title'],
            'dealhtml' => $VARS['content'],
            'validafter' => $vaf,
            'validbefore' => $vbf,
            'dealhasurl' => 0,
            'dealurl' => 'about:blank'
                ], [
            'AND' => [
                'dealid' => $VARS['dealid'],
                'merchantid' => $VARS['merchantid']
            ]
        ]);
    } else {
        $dealid = $database->insert(
                'deals', [
            'merchantid' => $VARS['merchantid'],
            'dealtitle' => $VARS['title'],
            'dealhtml' => $VARS['content'],
            'validafter' => $vaf,
            'validbefore' => $vbf,
            'dealhasurl' => 0,
            'dealurl' => 'about:blank'
                ]
        );
    }
    sendOK($dealid, true);
} else {
    sendError('No or invalid action specified.', true);
}

