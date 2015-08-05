<?php namespace FHTeam\SelectelStorageApi\Utility\Http;

use FHTeam\SelectelStorageApi\Exception\UnexpectedError;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Post\PostFile;

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
     * HttpRequest constructor.
     *
     * @param HttpClient $client
     * @param string     $method
     * @param string     $url
     */
    public function __construct(HttpClient $client, $method, $url)
    {
        $this->httpClient = $client;
        $this->guzzleRequest = $this->httpClient->createGuzzleRequest($method, $url, ['exceptions' => false]);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->guzzleResponse->getStatusCode();
    }

    /**
     * @param array $headers
     */
    public function setRequestHeaders(array $headers)
    {
        $this->guzzleRequest->setHeaders($headers);
    }

    public function addRequestHeader($name, $value)
    {
        $this->guzzleRequest->addHeader($name, $value);
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
    public function setBodyFromFile($fileName)
    {
        $handle = fopen($fileName, 'r');
        if (!$handle) {
            throw new UnexpectedError("Cannot open file '{$fileName}' due to unknown error");
        }

        $postFile = new PostFile(basename($fileName), $handle);
        $body = $postFile->getContent();
        $this->guzzleRequest->setBody($body);
    }

    /**
     * @return RequestInterface
     */
    public function getGuzzleRequest()
    {
        return $this->guzzleRequest;
    }

    /**
     * @param RequestInterface $guzzleRequest
     */
    public function setGuzzleRequest($guzzleRequest)
    {
        $this->guzzleRequest = $guzzleRequest;
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
}
