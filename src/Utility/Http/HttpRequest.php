<?php namespace FHTeam\SelectelStorageApi\Utility\Http;

use FHTeam\SelectelStorageApi\Exception\UnexpectedError;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpRequest
 *
 * @package ForumHouse\SelectelStorageApi\Utility\Http
 */
class HttpRequest
{
    const METHOD_POST = 'post';

    const METHOD_PUT = 'put';

    const METHOD_DELETE = 'delete';

    const METHOD_GET = 'get';

    const METHOD_PATCH = 'patch';

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var RequestInterface
     */
    protected $guzzleRequest;

    /**
     * @var ResponseInterface
     */
    protected $guzzleResponse;

    /**
     * @var false|string
     */
    protected $streamResponseBody = false;

    /**
     * HttpRequest constructor.
     *
     * @param HttpClient $client
     * @param string     $method
     * @param string     $url
     */
    public function __construct(HttpClient $client, $method, $url)
    {
        $this->httpClient = $client;
        $this->guzzleRequest = new Request($method, $url);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->guzzleResponse->getStatusCode();
    }

    public function getResponseBody()
    {
        if (null === $this->guzzleResponse->getBody()) {
            throw new UnexpectedError("Guzzle response doesn't have body");
        }

        return $this->guzzleResponse->getBody()->getContents();
    }

    /**
     * @param array $headers
     */
    public function setRequestHeaders(array $headers)
    {
        foreach ($headers as $headerName => $headerValue) {
            $this->guzzleRequest = $this->guzzleRequest->withAddedHeader($headerName, $headerValue);
        }
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addRequestHeader($name, $value)
    {
        $this->guzzleRequest = $this->guzzleRequest->withAddedHeader($name, $value);
    }

    /**
     * @return string[]
     */
    public function getResponseHeaders()
    {
        return $this->guzzleResponse->getHeaders();
    }

    /**
     * @param string $fileName
     *
     * @throws UnexpectedError
     */
    public function setRequestBodyFromFile($fileName)
    {
        $handle = fopen($fileName, 'r');
        if (!$handle) {
            throw new UnexpectedError("Cannot open file '{$fileName}' due to unknown error");
        }

        $this->guzzleRequest = $this->guzzleRequest->withBody(new Stream($handle));
    }

    /**
     * @return RequestInterface
     */
    public function getGuzzleRequest()
    {
        return $this->guzzleRequest;
    }

    /**
     * @return ResponseInterface
     */
    public function getGuzzleResponse()
    {
        return $this->guzzleResponse;
    }

    /**
     * @param ResponseInterface $guzzleResponse
     */
    public function setGuzzleResponse($guzzleResponse)
    {
        $this->guzzleResponse = $guzzleResponse;
    }

    /**
     * @return false|string
     */
    public function getStreamResponseBody()
    {
        return $this->streamResponseBody;
    }

    /**
     * @param false|string $streamResponseBody
     */
    public function setStreamResponseBody($streamResponseBody)
    {
        $this->streamResponseBody = $streamResponseBody;
    }
}
