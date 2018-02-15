<?php

namespace Ch3rm\Alexa\Request;

use Ch3rm\Alexa\Exceptions\RequestException;
use Ch3rm\Alexa\Request\Type\LaunchRequest;
use Ch3rm\Alexa\Request\Type\IntentRequest;
use Ch3rm\Alexa\Request\Type\SessionEndedRequest;

class Request
{
    private $request;
    private $version;
    private $sessionState;
    private $sessionId;
    private $appId;
    private $userId;
    private $deviceId;
    private $supportedInterfaces;
    private $apiEndpoint;
    private $apiToken;
    private $requestObject;
    private $requestType;

    public function __construct(string $request)
    {
        if(!$request = json_decode($request, true)){
            throw new RequestException('could not decode $request. $request does not seem to be a valid JSON string');
        }

        if(
            !isset($request['version']) ||
            !isset($request['session']) ||
            !isset($request['context']) ||
            !isset($request['request'])
        ){
            throw new RequestException('Request body seems to be malformed');
        }

        $this->request = $request;
        $this->version = $request['version'];
        $this->sessionState = $request['session']['new'];
        $this->sessionId = $request['session']['sessionId'];
        $this->appId = $request['session']['application']['applicationId'];
        $this->userId = $request['session']['user']['userId'];
        $this->deviceId = $request['context']['System']['device']['deviceId'];
        $this->supportedInterfaces = $request['context']['System']['device']['supportedInterfaces'];
        $this->apiEndpoint = $request['context']['System']['apiEndpoint'];
        $this->apiToken = $request['context']['System']['apiAccessToken'];
        $this->requestType = $request['request']['type'];

    }

    public function getRequestType()
    {
        return $this->requestType;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getSessionState()
    {
        return $this->sessionState;
    }

    public function sessionIsNew()
    {
        if($this->sessionState == 1){
            return true;
        }

        return false;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function getApplicationId()
    {
        return $this->appId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getDeviceId()
    {
        return $this->deviceId;
    }

    public function getSupportedInterfaces()
    {
        return $this->supportedInterfaces;
    }

    public function getRequestObject()
    {
        switch($this->requestType){
            case 'LaunchRequest':
                $this->requestObject = new LaunchRequest($this->request);
                break;
            case 'IntentRequest':
                $this->requestObject = new IntentRequest($this->request);
                break;
            case 'SessionEndedRequest':
                $this->requestObject = new SessionEndedRequest($this->request);
                break;
            default:
                throw new RequestException('Request Method not allowed');
        }

        return $this->requestObject;
    }
}