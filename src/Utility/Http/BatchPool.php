<?php namespace ForumHouse\SelectelStorageApi\Utility\Http;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Pool;
use JsonSerializable;
use SplObjectStorage;

/**
 * Class BatchPool
 *
 * @package ForumHouse\SelectelStorageApi\Utility\Http
 */
class BatchPool
{
    /**
     * @var array|RequestInterface[]|SplObjectStorage
     */
    protected $requests;

    /**
     * @var array|JsonSerializable[]
     */
    protected $objects;

    /**
     * @var int[]
     */
    protected $validResponses;

    /**
     * BatchPool constructor.
     *
     * @param array|RequestInterface[]|SplObjectStorage $requests
     * @param array|JsonSerializable[]                  $objects
     * @param int[]                                     $validResponses
     */
    public function __construct(array $requests, array $objects, array $validResponses)
    {
        $this->requests = $requests;
        $this->objects = $objects;
        $this->validResponses = $validResponses;
    }

    /**
     * @return array Array with 'ok' and 'failed' keys
     */
    public function send()
    {
        $responses = Pool::batch(new HttpClient(), $this->requests);

        $result = [
            'ok' => [],
            'failed' => [],
        ];

        /** @var ResponseInterface $response */
        foreach ($responses as $key => $response) {
            if ($response instanceof RequestException) {
                /** @var RequestException $response */
                $result['failed'][] = [
                    'exception' => $response->getMessage(),
                    'url' => $response->hasResponse() ? $response->getResponse()->getEffectiveUrl() :
                        $response->getRequest()->getUrl(),
                    'object' => $this->objects[$key],
                    'status_code' => $response->hasResponse() ? $response->getResponse()->getStatusCode() : '0',
                    'reason' => $response->hasResponse() ? $response->getResponse()->getReasonPhrase() : '',
                ];
                continue;
            }

            if (in_array($response->getStatusCode(), $this->validResponses)) {
                $result['ok'][] = $this->objects[$key];
                continue;
            }

            $result['failed'][] = [
                'url' => $response->getEffectiveUrl(),
                'object' => $this->objects[$key],
                'status_code' => $response->getStatusCode(),
                'reason' => $response->getReasonPhrase(),
            ];
        }

        return $result;
    }
}
