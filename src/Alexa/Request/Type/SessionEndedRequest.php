<?php

namespace Ch3rm\Alexa\Request\Type;

class SessionEndedRequest
{
    private $type;
    private $requestId;
    private $timestamp;
    private $reason;
    private $locale;
    private $error;

    public function __construct(array $request)
    {
        $this->type = $request['request']['type'];
        $this->requestId = $request['request']['requestId'];
        $this->timestamp = $request['request']['timestamp'];
        $this->reason = $request['request']['reason'];
        $this->locale = $request['request']['locale'];
        $this->error = $request['request']['error'];
    }

        public function getType()
        {
            return $this->type;
        }

        public function getRequestId()
        {
            return $this->getRequestId();
        }

        public function getTimestamp()
        {
            return $this->timestamp;
        }

        public function getReason()
        {
            return $this->reason;
        }

        public function getLocale()
        {
            return $this->locale;
        }

        public function getError()
        {
            return $this->error;
        }
}