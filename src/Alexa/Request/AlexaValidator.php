<?php

namespace Ch3rm\Alexa\Request;

use Ch3rm\Alexa\Exceptions\RequestException;
use Ch3rm\Alexa\Exceptions\SignatureException;
use Ch3rm\Alexa\Exceptions\NetworkException;
use Exception;

class AlexaValidator
{
    /**
     * @var int
     */
    private $time;

    /**
     * @var array
     */
    private $requestHeader;

    /**
     * @var string
     */
    private $requestBody;

    /**
     * @var string
     */
    private $signatureCertUrl;

    /**
     * @var string
     */
    private $signature;

    /**
     * @var resource
     */
    private $publicKey;

    /**
     * @var string
     */
    private $applicationId;

    /**
     * @var string
     */
    private $certAltName = 'echo-api.amazon.com';

    /**
     * Validator constructor.
     * @param array $requestHeader
     * @param string $rawRequestBody
     */
    public function __construct(array $requestHeader, string $rawRequestBody)
    {
        $this->time             = time();
        $this->requestHeader    = $requestHeader;
        $this->requestBody      = $rawRequestBody;
        $this->applicationId    = false;
    }

    /**
     * @return bool
     * @throws Exception
     * @throws NetworkException
     * @throws RequestException
     * @throws SignatureException
     */
    final public function validateRequest()
    {
        $this->validateSignatureData();
        $this->validateSignatureCertificate();
        $this->validateSignature();

        if($this->applicationId){
            $this->validateAppId();
        }

        return true;
    }

    /**
     * @return bool
     * @throws Exception
     * @throws RequestException
     */
    public function validateAppId()
    {
        if(!$this->applicationId){
            throw new Exception('No ApplicationId has been set. Please use setAppId() method first');
        }

        $requestBodyArray = json_decode($this->requestBody, true);

        if(!isset($requestBodyArray['session']['application']['applicationId'])){
            throw new RequestException('No ApplicationId provided in request');
        }

        if($requestBodyArray['session']['application']['applicationId'] != $this->applicationId){
            throw new RequestException('Asserted ApplicationId does not match derived ApplicationId');
        }

        return true;
    }

    /**
     * @param string $applicationId
     * @return $this
     */
    public function setAppId(string $applicationId)
    {
        $this->applicationId = $applicationId;
        return $this;
    }

    /**
     * @param string $certAltName
     * @return $this
     */
    public function setCertAltName(string $certAltName)
    {
        $this->certAltName = $certAltName;
        return $this;
    }

    /**
     * @return bool
     * @throws NetworkException
     * @throws SignatureException
     */
    private function validateSignatureCertificate()
    {
        //download signing certificate
        if(!$cert = file_get_contents($this->signatureCertUrl)){
            throw new NetworkException('The certificate could not be downloaded for unknown reasons');
        }

        $certData = openssl_x509_parse($cert);

        //Check expiration
        if($this->time < $certData['validFrom_time_t'] || $this->time > $certData['validTo_time_t']){
            throw new SignatureException('The provided signing certificate is expired');
        }

        //check alternative name
        $regex = sprintf('/(%s)/', $this->certAltName);
        if(!preg_match($regex, $certData['extensions']['subjectAltName'])){
            throw new SignatureException('The domain ' . $this->certAltName . ' is not present in the SAN');
        }

        if(!$publicKey = openssl_get_publickey($cert)){
            throw new SignatureException('could not extract public key');
        }

        $this->publicKey = $publicKey;

        return true;
    }

    /**
     * @return bool
     * @throws SignatureException
     */
    private function validateSignatureData()
    {
        if(!isset($this->requestHeader['HTTP_SIGNATURECERTCHAINURL'][0])){
            throw new SignatureException('The request does not contain a signature cert url');
        }

        if(!isset($this->requestHeader['HTTP_SIGNATURE'][0])){
            throw new SignatureException('The request does not contain a signature string');
        }

        $pattern = '/^(H|h)(T|t){2,2}(P|p)(S|s):\/\/((S|s)3\.(A|a)(M|m)(A|a)(Z|z)(O|o)(N|n)(A|a)(W|w)(S|s)\.(C|c)(O|o)(M|m))(\:443)?\/(echo\.api)\/.*/';
        if(!preg_match($pattern, $this->requestHeader['HTTP_SIGNATURECERTCHAINURL'][0])){
            throw new SignatureException('The provided signature is malformed');
        }

        $this->signatureCertUrl = $this->requestHeader['HTTP_SIGNATURECERTCHAINURL'][0];
        $this->signature = $this->requestHeader['HTTP_SIGNATURE'][0];

        return true;
    }

    /**
     * @return bool
     * @throws SignatureException
     */
    private function validateSignature()
    {
        $assertedHashEncrypted = base64_decode($this->signature);

        $assertedHashDecrypted = false;
        if(!openssl_public_decrypt($assertedHashEncrypted,$assertedHashDecrypted, $this->publicKey)){
            throw new SignatureException('The signature string could not be decrypted');
        }

        $assertedHash = bin2hex($assertedHashDecrypted);
        $derivedHash = '3021300906052b0e03021a05000414' . sha1($this->requestBody);

        if($derivedHash != $assertedHash){
            throw new SignatureException('The derived hash does not match the asserted Hash');
        }

        return true;
    }
}