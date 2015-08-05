<?php namespace FHTeam\SelectelStorageApi\Service;

use FHTeam\SelectelStorageApi\Utility\Http\HttpClient;
use FHTeam\SelectelStorageApi\Utility\Http\HttpRequest;
use FHTeam\SelectelStorageApi\Utility\Http\Response;

/**
 * Selectel storage service class
 *
 * @package ForumHouse\SelectelStorageApi
 */
class AccountService extends AbstractService
{
    /**
     * WARNING: this method sets account secret key for ALL containers of the account
     *
     * @param string $secretKey
     */
    public function setAccountSecretKey($secretKey)
    {
        $client = new HttpClient();
        $client->setGoodHttpStatusCodes([Response::HTTP_NO_CONTENT]);
        $request = new HttpRequest($client, HttpRequest::METHOD_POST, $this->authentication->getStorageUrl());
        $request->setRequestHeaders(
            ['X-Auth-Token' => $this->authentication->getAuthToken(), 'X-Account-Meta-Temp-URL-Key' => $secretKey,]
        );
        $client->send($request);
    }
}
