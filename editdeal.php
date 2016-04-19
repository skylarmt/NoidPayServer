<?php

require 'required.php';
require 'killOnMerchantLoginError.php';

if (is_empty($VARS['dealid'])) {
    sendError("Missing required dealid.", true);
}

if ($VARS['action'] == 'get') {
    $deal = $database->select('deals', [
        'dealid',
        'dealtitle',
        'dealhtml',
        'validafter',
        'validbefore'
            ], [
        'AND' => [
            'dealid' => $VARS['dealid'],
            'merchantid' => $VARS['merchantid']
        ]
    ])[0];
    $deal['status'] = 'OK';
    return json_encode($deal);
} else if ($VARS['action'] == 'delete') {
    $database->delete('deals', ['AND' => ['merchantid' => $VARS['merchantid'], 'dealid' => $VARS['dealid']]]);
    sendOK('', true);
} else if ($VARS['action'] == 'edit') {
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
    $database->insert(
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
} else {
    sendError('No or invalid action specified.', true);
}

