<?php

require(__DIR__ . '/DemoFunctions.php');

ini_set('display_errors', true);

const REQUEST_LOCATION = 'Web';
const REQUEST_CONTAINER = 'Request';
const RESPONSE_LOCATION = 'Web';
const RESPONSE_CONTAINER = 'Response';
const TEMPLATE_LOCATION = 'UserInterface';
const TEMPLATE_CONTAINER = 'Template';
const OUTPUT_COMPONENT_LOCATION = 'Output';
const OUTPUT_COMPONENT_CONTAINER = 'Mock';

processFormIfSubmitted(getMockCrud());
echo getHtml();

function getBody(): string
{
    return '
                <body class="gradientBg">
                    <div id="welcome" class="genericContainer genericContainerLimitedHeight">' . getWelcomeMessage() . '</div>
                    <div id="requestMenu" class="genericContainer genericContainerLimitedHeight">' . getCurrentRequestInfo() . '</div>
                    ' . (empty(getStoredRequestMenu(getMockCrud())) ? "" : '<div class="genericContainer">' . getStoredRequestMenu(getMockCrud()) . '</div>') . '
                        ' . getCollectiveOutputFromOutputAssignedToResponsesToCurrentRequest() . '
                ' . (str_replace([' ', PHP_EOL], '', getScripts()) === '<script></script>' ? '' : getScripts() . PHP_EOL) . '
                </body>';
}

function getWelcomeMessage(): string
{
    return <<<'HTML'
    <h1 class="noticeText">Welcome</h1>
    <p class="successText">
        To see a working demo of some of the Darling Cms's core components go <a href="WorkingDemo.php">here</a>.
    </p>
HTML;
}



