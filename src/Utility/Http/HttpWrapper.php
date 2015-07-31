<?php namespace ForumHouse\SelectelStorageApi\Utility\Http;

use Exception;
use ForumHouse\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use GuzzleHttp\Client;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

class HttpWrapper
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var int[]
     */
    protected $expectedHttpCodes;

    /**
     * @var string[]
     */
    protected $customErrors;

    /**
     * @var RequestInterface
     */
    protected $currentRequest;

    /**
     * @var ResponseInterface
     */
    protected $currentResponse;

    /**
     * HttpWrapper constructor.
     */
    public function __construct()
    {
        $this->client = new Client;
    }

    /**
     * @param int[] $codes
     *
     * @return HttpWrapper
     */
    public function setExpectedHttpCodes(array $codes)
    {
        $this->expectedHttpCodes = $codes;

        return $this;
    }

    /**
     * @param int    $httpCode
     * @param string $exceptionClassName
     */
    public function addCustomError($httpCode, $exceptionClassName)
    {
        $this->customErrors[$httpCode] = $exceptionClassName;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->currentResponse->getStatusCode();
    }

    /**
     * @return string[]
     */
    public function getResponseHeaders()
    {
        return $this->currentRequest->getHeaders();
    }

    /**
     * Send body-less http request
     *
     * @param string   $method
     * @param string   $url
     * @param string[] $headers
     *
     * @return bool
     * @throws Exception
     */
    public function sendPlainHttpRequest($method, $url, array $headers = [])
    {
        $this->currentRequest = $this->client->createRequest($method, $url, ['exceptions' => false]);
        $this->currentRequest->addHeaders($headers);

        $this->currentResponse = $this->client->send($this->currentRequest);

        $statusCode = $this->currentResponse->getStatusCode();

        if (in_array($statusCode, $this->expectedHttpCodes)) {
            return true;
        }

        throw  $this->getException($statusCode, $method, $url, $headers);
    }

    /**
     * @param int $statusCode
     *
     * @return Exception|UnexpectedHttpStatusException
     */
    protected function getException($statusCode, $method, $url, $headers)
    {
        if (isset($this->customErrors[$statusCode])) {
            $className = $this->customErrors[$statusCode];

            return new $className;
        }

        return new UnexpectedHttpStatusException(
            $statusCode,
            "Unexpected http status '$statusCode' from [$method] '$url'. Headers are: ".json_encode($headers)
        );
    }
}
