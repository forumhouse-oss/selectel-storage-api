<?php namespace FHTeam\SelectelStorageApi\Service\File;

use Exception;
use FHTeam\SelectelStorageApi\Exception\FileCrcFailedException;
use FHTeam\SelectelStorageApi\Exception\ParallelOperationException;
use FHTeam\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use FHTeam\SelectelStorageApi\Objects\Container;
use FHTeam\SelectelStorageApi\Objects\File;
use FHTeam\SelectelStorageApi\Service\AbstractService;
use FHTeam\SelectelStorageApi\Utility\Http\HttpClient;
use FHTeam\SelectelStorageApi\Utility\Http\HttpRequest;
use FHTeam\SelectelStorageApi\Utility\Http\Response;
use FHTeam\SelectelStorageApi\Utility\UrlTools;

/**
 * Class UploadService
 *
 * @package ForumHouse\SelectelStorageApi\Service\File
 */
class FileUploadService extends AbstractService
{
    /**
     * Uploads a file to a container
     *
     * @param Container $container Container with its name attribute set
     * @param File      $file      File with its localName and size attributes set
     *
     * @return true True if file is uploaded successfully
     * @throws Exception
     * @throws UnexpectedHttpStatusException
     */
    public function uploadFile(Container $container, File $file)
    {
        if (!$file->getSize()) {
            throw new Exception("File should have size set for upload operation");
        }

        $client = new HttpClient();

        $request = $this->createRequestUploadFile($container, $file, $client);
        $client->send($request);
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
        $client = new HttpClient();
        foreach ($files as $file) {
            $requests[] = $this->createRequestUploadFile($container, $file, $client);
            $objects[] = $file;
        }
        $result = $client->sendMany($requests, $objects);

        if (count($result['failed']) < 1) {
            return;
        }

        // ==== ERROR HANDLING ====
        $deletionService = new FileDeleteService($this->authentication);
        if ($atomically) {
            foreach ($result['ok'] as $file) {
                try {
                    $deletionService->deleteFile($container, $file);
                } catch (Exception $e) {
                    //Silent
                }
            }
        }

        throw new ParallelOperationException('uploadFiles', $result['failed']);
    }

    /**
     * @param Container  $container
     * @param File       $file
     * @param HttpClient $client
     *
     * @return HttpRequest
     */
    protected function createRequestUploadFile(
        Container $container,
        File $file,
        HttpClient $client
    ) {
        $url = UrlTools::getServerResourceUrl($this->authentication, $container, $file);
        $request = new HttpRequest($client, HttpRequest::METHOD_PUT, $url);
        $request->setRequestHeaders($file->getHeaders());
        $request->setBodyFromFile($file->getLocalName());
        $request->addRequestHeader('X-Auth-Token', $this->authentication->getAuthToken());

        $client->setGoodHttpStatusCodes([Response::HTTP_CREATED]);
        $client->setCustomErrors([Response::HTTP_UNPROCESSABLE_ENTITY => FileCrcFailedException::class]);

        return $request;
    }
}
