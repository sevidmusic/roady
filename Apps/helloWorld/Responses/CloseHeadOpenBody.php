<?php

$appComponentsFactory->buildGlobalResponse(
    'CloseHeadOpenBody',
    2,
    $appComponentsFactory->buildStandardUITemplate(
        'StandardUITemplate',
        'Components',
        0,
        $appComponentsFactory->buildOutputComponent(
            'OutputComponent',
            'Components',
            '',
            0
        ),
    ),
    $appComponentsFactory->buildOutputComponent(
        'ClosingHeadTag',
        'Components',
        '</head>',
        0
    ),
    $appComponentsFactory->buildOutputComponent(
        'OpeningBodyTag',
        'Components',
        '<body>',
        0.1
    ),
);

