<?php namespace FHTeam\SelectelStorageApi\Service;

use FHTeam\SelectelStorageApi\Exception\ParallelOperationException;
use FHTeam\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use FHTeam\SelectelStorageApi\Objects\Container;
use FHTeam\SelectelStorageApi\Objects\SymLink;
use FHTeam\SelectelStorageApi\Utility\Http\HttpClient;
use FHTeam\SelectelStorageApi\Utility\Http\HttpRequest;
use FHTeam\SelectelStorageApi\Utility\Http\Response;
use FHTeam\SelectelStorageApi\Utility\UrlTools;

class SymLinkService extends AbstractService
{
    /**
     * Creates a symlink to a file in the container
     *
     * @param \FHTeam\SelectelStorageApi\Objects\Container $container
     * @param SymLink                                      $link
     *
     * @return bool
     * @throws UnexpectedHttpStatusException
     */
    public function createSymlink(Container $container, SymLink $link)
    {
        //TODO: add ability to provide symlink file name
        $client = new HttpClient();
        $client->setGoodHttpStatusCodes([Response::HTTP_CREATED]);
        $url = UrlTools::getServerResourceUrl($this->authentication, $container, $link);
        $request = new HttpRequest($client, HttpRequest::METHOD_PUT, $url);
        $request->setRequestHeaders($link->getHeaders());
        $request->addRequestHeader('X-Auth-Token', $this->authentication->getAuthToken());
        $client->send($request);
    }

    /**
     * @param \FHTeam\SelectelStorageApi\Objects\Container $container
     * @param \FHTeam\SelectelStorageApi\Objects\Symlink[] $links
     *
     * @throws ParallelOperationException
     */
    public function createSymLinks(Container $container, array $links)
    {
        //TODO: add ability to provide symlink file name
        $requests = [];
        $objects = [];
        $client = new HttpClient();
        $client->setGoodHttpStatusCodes([Response::HTTP_CREATED]);
        foreach ($links as $link) {
            $url = UrlTools::getServerResourceUrl($this->authentication, $container, $link);
            $request = new HttpRequest($client, HttpRequest::METHOD_PUT, $url);
            $request->addRequestHeader('X-Auth-Token', $this->authentication->getAuthToken());
            $requests[] = $request;

            $objects[] = $link;
        }

        $result = $client->sendMany($requests, $objects);

        if (count($result['failed']) < 1) {
            return;
        }

        throw new ParallelOperationException('deleteFiles', $result['failed']);
    }
}
