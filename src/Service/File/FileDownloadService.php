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

class FileDownloadService extends AbstractService
{
    /**
     * @param Container $container
     * @param File      $file
     *
     * @throws UnexpectedHttpStatusException
     * @throws \Exception
     */
    public function downloadFile(Container $container, File $file)
    {
        $client = new HttpClient();
        $client->setGoodHttpStatusCodes([Response::HTTP_OK]);
        $request = $this->createFileDownloadRequest($client, $container, $file);

        $client->send($request);
    }

    /**
     * @param Container $container
     * @param File[]    $files
     *
     * @return array
     * @throws \Exception
     * @throws UnexpectedHttpStatusException
     */
    public function downloadFiles(Container $container, array $files)
    {
        $client = new HttpClient();
        $client->setGoodHttpStatusCodes([Response::HTTP_OK]);
        $requests = [];
        foreach ($files as $key => $file) {
            $requests[$key] = $this->createFileDownloadRequest($client, $container, $file);
        }

        $result = $client->sendMany($requests, $files);

        if (count($result['failed']) < 1) {
            return;
        }

        throw new ParallelOperationException('downloadFiles', $result['failed']);
    }

    /**
     * @param HttpClient $client
     * @param Container  $container
     * @param File       $file
     *
     * @return HttpRequest
     */
    protected function createFileDownloadRequest(HttpClient $client, Container $container, File $file)
    {
        $url = UrlTools::getServerResourceUrl($this->authentication, $container, $file);
        $request = new HttpRequest($client, HttpRequest::METHOD_GET, $url);
        $request->setStreamResponseBody($file->getLocalName());
        $request->addRequestHeader('X-Auth-Token', $this->authentication->getAuthToken());

        return $request;
    }
}
