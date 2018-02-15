<?php

namespace Ch3rm\Alexa\Request\Type;

class IntentRequest
{
    private $type;
    private $requestId;
    private $timestamp;
    private $dialogState;
    private $locale;
    private $name;
    private $confirmationStatus;
    private $slots;

    public function __construct(array $request)
    {
        $this->type = $request['request']['type'];
        $this->requestId = $request['request']['requestId'];
        $this->timestamp = $request['request']['timestamp'];

        if(isset($request['request']['dialogState'])){
            $this->dialogState = $request['request']['dialogState'];
        }

        $this->locale = $request['request']['locale'];
        $this->name = $request['request']['intent']['name'];
        $this->confirmationStatus = $request['request']['intent']['confirmationStatus'];
        $this->slots = json_decode(json_encode($request['request']['intent']['slots']));
    }

    public function getType()
    {
        return $this->type;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getDialogState()
    {
        return $this->dialogState;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getConfirmationStatus()
    {
        return $this->confirmationStatus;
    }

    public function getSlots()
    {
        return $this->slots;
    }
}