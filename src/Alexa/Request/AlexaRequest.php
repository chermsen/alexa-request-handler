<?php

namespace Ch3rm\Alexa\Request;

use Ch3rm\Alexa\Exceptions\RequestException;
use Ch3rm\Alexa\Request\Type\LaunchRequest;
use Ch3rm\Alexa\Request\Type\IntentRequest;
use Ch3rm\Alexa\Request\Type\SessionEndedRequest;

/**
 * Class AlexaRequest
 *
 * @package Ch3rm\Alexa\Request
 */
class AlexaRequest
{
    /**
     * @var array
     */
    private $request;

    /**
     * @var string
     */
    private $version;

    /**
     * @var bool
     */
    private $sessionState;

    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var
     */
    private $deviceId;

    /**
     * @var array
     */
    private $supportedInterfaces;

    /**
     * @var string
     */
    private $apiEndpoint;

    /**
     * @var
     */
    private $apiToken;

    /**
     * @var object
     */
    private $requestObject;

    /**
     * @var string
     */
    private $requestType;

    /**
     * AlexaRequest constructor
     *
     * @param string $request
     * @throws RequestException
     */
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

    /**
     * @return string
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function getSessionState()
    {
        return $this->sessionState;
    }

    /**
     * @return bool
     */
    public function sessionIsNew()
    {
        if($this->sessionState == 1){
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @return string
     */
    public function getApplicationId()
    {
        return $this->appId;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @return array
     */
    public function getSupportedInterfaces()
    {
        return $this->supportedInterfaces;
    }

    /**
     * @return IntentRequest|LaunchRequest|SessionEndedRequest|object
     * @throws RequestException
     */
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