<?php

namespace ForumHouse\SelectelStorageApi\Service;

use ForumHouse\SelectelStorageApi\Authentication\IAuthentication;
use ForumHouse\SelectelStorageApi\Container\Container;
use ForumHouse\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use ForumHouse\SelectelStorageApi\File\Exception\CrcFailedException;
use ForumHouse\SelectelStorageApi\File\File;
use ForumHouse\SelectelStorageApi\Utility\HttpClient;
use ForumHouse\SelectelStorageApi\Utility\Response;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Post\PostBody;
use GuzzleHttp\Post\PostBodyInterface;
use GuzzleHttp\Post\PostFile;

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
     * @throws \Exception
     * @throws \ForumHouse\SelectelStorageApi\Exception\UnexpectedError
     * @return true True if file is uploaded successfully
     */
    public function uploadFile(Container $container, File $file)
    {
        if (!$file->getSize()) {
            throw new \Exception("File should have size set for upload operation");
        }

        $request = $this->createHttpRequest('put', $container, $file);
        /** @var PostBodyInterface $postBody */
        $postBody = new PostBody();
        $postBody->addFile(new PostFile(basename($file->getLocalName()), $file->openLocal('r')));
        $request->setBody($postBody);
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
     * @param Container $container
     * @param File      $file
     *
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
     * @param string    $method
     * @param Container $container
     * @param File      $file
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createHttpRequest($method, Container $container, File $file)
    {
        $request = $this->httpClient->createRequest($method,
            $this->authentication->getStorageUrl() . '/' . $container->getName() . '/' . $file->getServerName());
        $request->addHeader('X-Auth-Token', $this->authentication->getAuthToken());
        return $request;
    }
}
