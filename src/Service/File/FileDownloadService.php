<?php namespace FHTeam\SelectelStorageApi\Service\File;

use FHTeam\SelectelStorageApi\Objects\Container;
use FHTeam\SelectelStorageApi\Objects\File;
use FHTeam\SelectelStorageApi\Service\AbstractService;
use FHTeam\SelectelStorageApi\Utility\Http\HttpClient;
use FHTeam\SelectelStorageApi\Utility\Http\HttpRequest;
use FHTeam\SelectelStorageApi\Utility\Http\Response;
use FHTeam\SelectelStorageApi\Utility\UrlTools;

class FileDownloadService extends AbstractService
{
    public function downloadFile(Container $container, File $file)
    {
        $client = new HttpClient();
        $client->setGoodHttpStatusCodes([Response::HTTP_OK]);
        $url = UrlTools::getServerResourceUrl($this->authentication, $container, $file);
        $request = new HttpRequest($client, HttpRequest::METHOD_GET, $url);
        $request->addRequestHeader('X-Auth-Token', $this->authentication->getAuthToken());

        return $request;
    }
}
