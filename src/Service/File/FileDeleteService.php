<?php namespace FHTeam\SelectelStorageApi\Service\File;

use FHTeam\SelectelStorageApi\Exception\ParallelOperationException;
use FHTeam\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use FHTeam\SelectelStorageApi\Objects\Container;
use FHTeam\SelectelStorageApi\Objects\File;
use FHTeam\SelectelStorageApi\Service\AbstractService;
use FHTeam\SelectelStorageApi\Utility\Http\HttpClient;
use FHTeam\SelectelStorageApi\Utility\Http\HttpRequest;
use FHTeam\SelectelStorageApi\Utility\Http\Response;
use FHTeam\SelectelStorageApi\Utility\UrlTools;

class FileDeleteService extends AbstractService
{
    /**
     * @param Container $container
     * @param File      $file
     *
     * @return bool
     * @throws UnexpectedHttpStatusException
     */
    public function deleteFile(Container $container, File $file)
    {
        $client = new HttpClient();
        $client->setGoodHttpStatusCodes([Response::HTTP_NO_CONTENT, Response::HTTP_NOT_FOUND]);
        $url = UrlTools::getServerResourceUrl($this->authentication, $container, $file);
        $request = new HttpRequest($client, HttpRequest::METHOD_DELETE, $url);
        $request->addRequestHeader('X-Auth-Token', $this->authentication->getAuthToken());
        $client->send($request);

        return $request->getStatusCode() === Response::HTTP_NO_CONTENT ? true : false;
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
        $client = new HttpClient();
        $client->setGoodHttpStatusCodes([Response::HTTP_NO_CONTENT, Response::HTTP_NOT_FOUND]);

        foreach ($files as $file) {
            $url = UrlTools::getServerResourceUrl($this->authentication, $container, $file);
            $request = new HttpRequest($client, HttpRequest::METHOD_DELETE, $url);
            $request->addRequestHeader('X-Auth-Token', $this->authentication->getAuthToken());
            $requests[] = $request;
            $objects[] = $file;
        }

        $result = $client->sendMany($requests, $objects);

        if (count($result['failed']) < 1) {
            return;
        }

        throw new ParallelOperationException('deleteFiles', $result['failed']);
    }
}
