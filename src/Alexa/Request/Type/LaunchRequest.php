<?php

namespace Ch3rm\Alexa\Request\Type;

class LaunchRequest
{
    private $type;
    private $requestId;
    private $timestamp;
    private $locale;

    public function __construct(array $request)
    {
        $this->type = $request['request']['type'];
        $this->requestId = $request['request']['type'];
        $this->timestamp = $request['request']['timestamp'];
        $this->local = $request['request']['locale'];
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

    public function getLocale()
    {
        return $this->locale;
    }
}