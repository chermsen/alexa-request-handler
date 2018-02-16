# Alexa-Request-Handler

This library provides classes and methods for validating, evaluating, and responding to Amazon Alexa requests.

## Installing

Install the latest Version with

```bash
$ composer require ch3rm/alexa/alexa-request-handler
```

## Usage 

### Validation
To validate a request according to Amazon's recommendations:

```php
<?php

use Ch3rm\Alexa\Request\AlexaValidator;
use Ch3rm\Alexa\Exceptions\SignatureException;
use Ch3rm\Alexa\Exceptions\RequestException;

//Get body of request
$requestBody = file_get_contents('php:://input');

//Validate request
try{
    $validator = new AlexaValidator($_SERVER, $requestBody);
    $validator->validateRequest();
}
catch(SignatureException | RequestException $e){
    http_response_code(400);
    die('Bad Request');
}
```
This few lines perform the following steps:

* Verification of the URL for the signature certificate
* Download and verification of the signature certificate (SAN, EXP)
* Extraction of the public key
* Decryption of the hash from the header field' HTTP_SIGNATURE'.
* Comparison of the given hash with the request body hash

Take a look at: ([Checking the Signature of the Request](https://developer.amazon.com/de/docs/custom-skills/host-a-custom-skill-as-a-web-service.html#checking-the-signature-of-the-request))

Since the Validator class does not yet know the AppId for this character, it can not validate it. To make the AppId known, 
the setAppId () method must be used:

```php
    $validator = new AlexaValidator($_SERVER, $requestBody)
    $validator->setApplicationId('someAppId')
    $validator->validateRequest();
``` 

**IMPORTANT**: All returns are based on exceptions. Whenever one of the above checks fails, either an exception of type 
SignatureException (checks related to the Amazon signature process) or of type RequestException 
(any problem with the received request) is thrown.

Please keep this in mind during development by using appropriate try-catch blocks.    

### Handle the request

```php
<?php

use Ch3rm\Alexa\Request\AlexaRequest;
use Ch3rm\Alexa\Request\Type\LaunchRequest;
use Ch3rm\Alexa\Request\Type\IntentRequest;
use Ch3rm\Alexa\Request\Type\SessionEndedRequest;

//Get body of request
$requestBody = file_get_contents('php:://input');

//Get request requested action
$alexaRequest = new AlexaRequest($requestBody);
$requestAction = $alexaRequest->getRequestObject();

//Check with type of request was sent
if($requestAction instanceof LaunchRequest){
    //do something
} elseif($requestAction instanceof IntentRequest){
    //do something else
} elseif (($requestAction instanceof SessionEndedRequest)){
    //do something completely different
} else{
    //Excuse, because this lib can only handle the 3 types mentioned above at the moment 
}
```

To create a valid response:

```php
<?php

use Ch3rm\Alexa\Response\AlexaResponse;

$alexaResponse = new AlexaResponse();
$alexaResponse
    ->withOutputSpeech('Some Text')
    ->withReprompt('Some Text')
    ->withSimpleCard('Title', 'Content')
    ->withShouldEndSession();

return $alexaResponse->getResponse();

```

## Authors

Christian Hermsen - [christian.hermsen@gmail.com](mailto:christian.hermsen@gmail.com) - https://hermsen.online

## License

This project is licensed under the APACHE 2.0 License - see the [LICENSE](LICENSE) file for details