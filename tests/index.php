<?php

require_once '../vendor/autoload.php';

$jsonRequestHeader = file_get_contents('RequestExamples/requestHeader.json');
$requestHeader = json_decode($jsonRequestHeader, true);

$requestBody = file_get_contents('RequestExamples/dotRequestStarted.json');

$validator = new \Ch3rm\Alexa\Request\Validator($requestHeader, $requestBody);
$validator->validateRequest();
