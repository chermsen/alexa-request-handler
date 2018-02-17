<?php

namespace Ch3rm\Alexa\Response;

/**
 * Class AlexaResponse
 *
 * The response class provides a valid and consistent Alexa Response object at any time
 *
 * @package Ch3rm\Alexa\Response
 */
class AlexaResponse
{
    /**
     * @var array
     */
    private $response;

    /**
     * @var string
     */
    private $version = '1.0';

    /**
     * AlexaResponse constructor
     */
    public function __construct()
    {
        $this->response = [
            'version' => $this->version,
            'response' => []
        ];
    }

    /**
     * @param string $text
     * @param bool $ssml
     * @return $this
     */
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

    /**
     * @param $title
     * @param $content
     * @return $this
     */
    public function withSimpleCard($title, $content)
    {
        $this->response['response']['card'] = [
            'type' => 'Simple',
            'title' => (string) $title,
            'content' => (string) $content
        ];

        return $this;
    }

    /**
     * @param $title
     * @param $content
     * @param array $image
     * @return $this
     */
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

    /**
     * @param string $text
     * @param bool $ssml
     * @return $this
     */
    public function withReprompt(string $text, bool $ssml = false)
    {
        if($ssml){
            $type = 'SSML';
            $fieldName = 'ssml';
        } else{
            $type = 'PlainText';
            $fieldName = 'text';
        }

        $this->response['response']['reprompt']['outputSpeech'] = [
            'type' => $type,
            $fieldName => (string) $text,
        ];

        return $this;
    }

    /**
     * @param array $directives
     * @return $this
     */
    public function withDirectives(array $directives)
    {
        $this->response['response']['directives'] = $directives;
        return $this;
    }

    /**
     * @param bool $var
     * @return $this
     */
    public function withShouldEndSession(bool $var = true)
    {
        $this->response['response']['shouldEndSession'] = $var;

        return $this;
    }

    /**
     * @param string $version
     * @return $this
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @param bool $json
     * @return array|mixed
     */
    public function getResponse(bool $json = true)
    {
        if(!$json){
            return $this->response;
        }

        return json_encode($this->response);

    }
}