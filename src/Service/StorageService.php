<?php

namespace ForumHouse\SelectelStorageApi\Service;

use Exception;
use ForumHouse\SelectelStorageApi\Authentication\IAuthentication;
use ForumHouse\SelectelStorageApi\Container\Container;
use ForumHouse\SelectelStorageApi\Exception\ParallelOperationException;
use ForumHouse\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use ForumHouse\SelectelStorageApi\File\Exception\CrcFailedException;
use ForumHouse\SelectelStorageApi\File\File;
use ForumHouse\SelectelStorageApi\File\ServerResourceInterface;
use ForumHouse\SelectelStorageApi\File\SymLink;
use ForumHouse\SelectelStorageApi\Utility\HttpClient;
use ForumHouse\SelectelStorageApi\Utility\Response;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Pool;
use GuzzleHttp\Post\PostBodyInterface;
use GuzzleHttp\Post\PostFile;
use JsonSerializable;
use SplObjectStorage;

/**
 * Selectel storage service class
 *
 * @package ForumHouse\SelectelStorageApi
 */
class StorageService
{
    /**
     * @var IAuthentication
     */
    protected $authentication;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @param IAuthentication $authentication
     */
    public function __construct(IAuthentication $authentication)
    {
        $this->authentication = $authentication;
        $this->httpClient = new HttpClient();
    }

    /**
     * Uploads a file to a container
     *
     * @param Container $container Container with its name attribute set
     * @param File      $file      File with its localName and size attributes set
     *
     * @throws CrcFailedException
     * @throws UnexpectedHttpStatusException
     * @throws Exception
     * @throws \ForumHouse\SelectelStorageApi\Exception\UnexpectedError
     * @return true True if file is uploaded successfully
     */
    public function uploadFile(Container $container, File $file)
    {
        if (!$file->getSize()) {
            throw new Exception("File should have size set for upload operation");
        }

        $request = $this->createRequestUploadFile($container, $file);

        /** @var ResponseInterface $response */
        $response = $this->httpClient->send($request);
        $statusCode = $response->getStatusCode();
        switch ($statusCode) {
            case Response::HTTP_CREATED:
                return true;
            case Response::HTTP_UNPROCESSABLE_ENTITY:
                throw new CrcFailedException($file->getLocalName());
            default:
                throw new UnexpectedHttpStatusException($statusCode, $response->getReasonPhrase());
        }
    }

    /**
     * Uploads several files simultaneously into a container. HTTP requests are executed in parallel
     *
     * @param Container $container
     * @param File[]    $files
     * @param bool      $atomically
     *
     * @return bool
     * @throws ParallelOperationException
     */
    public function uploadFiles(Container $container, array $files, $atomically)
    {
        $requests = [];
        $objects = [];
        foreach ($files as $file) {
            $requests[] = $this->createRequestUploadFile($container, $file);
            $objects[] = $file;
        }

        $result = $this->getPoolResults($requests, $objects, [Response::HTTP_CREATED]);

        if (count($result['failed']) < 1) {
            return;
        }

        if ($atomically) {
            foreach ($result['ok'] as $file) {
                try {
                    $this->deleteFile($container, $file);
                } catch (Exception $e) {
                    //Silent
                }
            }
        }

        throw new ParallelOperationException('uploadFiles', $result['failed']);
    }

    /**
     * @param Container $container
     * @param File      $file
     *
     * @return bool
     * @throws UnexpectedHttpStatusException
     */
    public function deleteFile(Container $container, File $file)
    {
        $request = $this->createHttpRequest('delete', $container, $file);

        /** @var ResponseInterface $response */
        $response = $this->httpClient->send($request);
        $statusCode = $response->getStatusCode();
        switch ($statusCode) {
            case Response::HTTP_NO_CONTENT:
                return true;
            case Response::HTTP_NOT_FOUND:
                return false;
            default:
                throw new UnexpectedHttpStatusException($statusCode, $response->getReasonPhrase());
        }
    }

    /**
     * @param Container $container
     * @param array     $files
     *
     * @throws ParallelOperationException
     */
    public function deleteFiles(Container $container, array $files)
    {
        $requests = [];
        $objects = [];
        foreach ($files as $file) {
            $requests[] = $this->createHttpRequest('delete', $container, $file);
            $objects[] = $file;
        }

        $result = $this->getPoolResults($requests, $objects, [Response::HTTP_NO_CONTENT, Response::HTTP_NOT_FOUND]);

        if (count($result['failed']) < 1) {
            return;
        }

        throw new ParallelOperationException('deleteFiles', $result['failed']);
    }

    /**
     * Creates a symlink to a file in the container
     *
     * @param Container $container
     * @param SymLink   $link
     *
     * @return bool
     * @throws UnexpectedHttpStatusException
     */
    public function createSymlink(Container $container, SymLink $link)
    {
        $request = $this->createRequestMakeSymlink($container, $link);

        /** @var ResponseInterface $response */
        $response = $this->httpClient->send($request);
        $statusCode = $response->getStatusCode();
        switch ($statusCode) {
            case Response::HTTP_CREATED:
                return true;
            default:
                throw new UnexpectedHttpStatusException($statusCode, $response->getReasonPhrase());
        }
    }

    /**
     * @param Container $container
     * @param Symlink[] $links
     *
     * @throws ParallelOperationException
     */
    public function createSymLinks(Container $container, array $links)
    {
        $requests = [];
        $objects = [];
        foreach ($links as $link) {
            $requests[] = $this->createRequestMakeSymlink($container, $link);
            $objects[] = $link;
        }

        $result = $this->getPoolResults($requests, $objects, [Response::HTTP_CREATED]);

        if (count($result['failed']) < 1) {
            return;
        }

        throw new ParallelOperationException('deleteFiles', $result['failed']);
    }

    /**
     * @param string                  $method    HTTP method
     * @param Container               $container Container for the request
     * @param ServerResourceInterface $file      Object for the request
     *
     * @return RequestInterface
     * @throws Exception
     */
    protected function createHttpRequest($method, Container $container, ServerResourceInterface $file)
    {
        $url = $this->authentication->getStorageUrl().'/'.$container->getName().'/'.$file->getServerName();
        $request = $this->httpClient->createRequest($method, $url, ['exceptions' => false]);
        $request->addHeader('X-Auth-Token', $this->authentication->getAuthToken());

        return $request;
    }

    /**
     * @param Container $container
     * @param File      $file
     *
     * @return RequestInterface
     * @throws \ForumHouse\SelectelStorageApi\Exception\UnexpectedError
     */
    protected function createRequestUploadFile(Container $container, File $file)
    {
        $request = $this->createHttpRequest('put', $container, $file);
        /** @var PostBodyInterface $postBody */
        $postFile = new PostFile(basename($file->getLocalName()), $file->openLocal('r'));
        $body = $postFile->getContent();
        $request->setBody($body);
        $request->addHeaders($file->getHeaders());

        return $request;
    }

    /**
     * @param Container $container
     * @param SymLink   $link
     *
     * @return RequestInterface
     */
    protected function createRequestMakeSymlink(Container $container, SymLink $link)
    {
        $request = $this->createHttpRequest('put', $container, $link);
        /** @var PostBodyInterface $postBody */
        $request->addHeaders($link->getHeaders());

        return $request;
    }

    /**
     * @param array|RequestInterface[]|SplObjectStorage $requests
     * @param array|JsonSerializable[]                  $objects
     * @param int[]                                     $validResponses
     *
     * @return array Array with 'ok' and 'failed' keys
     */
    protected function getPoolResults(array $requests, array $objects, array $validResponses)
    {
        $responses = Pool::batch($this->httpClient, $requests);

        $result = [
            'ok' => [],
            'failed' => [],
        ];

        /** @var ResponseInterface $response */
        foreach ($responses as $key => $response) {
            if (in_array($response->getStatusCode(), $validResponses)) {
                $result['ok'][] = $objects[$key];
                continue;
            }

            $result['failed'][] = [
                $objects[$key],
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ];
        }

        return $result;
    }
}
