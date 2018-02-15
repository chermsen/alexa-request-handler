<?php

namespace Ch3rm\Alexa\Response;

class Response
{
    private $response;
    private $version = '1.0';

    const EOL = "\r\n";

    public function __construct()
    {
        $this->response = [
            'version' => $this->version,
            'response' => []
        ];
    }

    public function withOutputSpeech(string $text, bool $ssml = false)
    {
        if($ssml){
            $type = 'SSML';
            $fieldName = 'ssml';
        } else{
            $type = 'PlainText';
            $fieldName = 'text';
        }

        $this->response['response']['outputSpeech'] = [
            'type' => $type,
            $fieldName => (string) $text,
        ];

        return $this;
    }

    public function withSimpleCard($title, $content)
    {
        $this->response['response']['card'] = [
            'type' => 'Simple',
            'title' => (string) $title,
            'content' => (string) $content
        ];

        return $this;
    }

    public function withStandardCard($title, $content, array $image = [])
    {
        $this->response['response']['card'] = [
            'type' => 'Standard',
            'title' => (string) $title,
            'text' => (string) $content,
        ];

        if(
            !empty($image) &&
            count($image) == 2 &&
            array_key_exists('smallImageUrl', $image) &&
            array_key_exists('largeImageUrl', $image)
        ){
            $this->response['response']['card']['image'] = $image;
        }

        return $this;
    }

    public function withReprompt(string $text, bool $ssml = false)
    {
        if($ssml){
            $type = 'SSML';
            $fieldName = 'ssml';
        } else{
            $type = 'PlainText';
            $fieldName = 'text';
        }

        $this->response['response']['reprompt'] = [
            'type' => $type,
            $fieldName => (string) $text,
        ];

        return $this;
    }

    public function withDirectives(array $directives)
    {
        $this->response['response']['directives'] = $directives;
        return $this;
    }

    public function withShouldEndSession(bool $var = true)
    {
        $this->response['response']['shouldEndSession'] = $var;

        return $this;
    }

    public function renderResponse()
    {
        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($this->response);
    }
}