<?php namespace ForumHouse\SelectelStorageApi\Authentication;

use ForumHouse\SelectelStorageApi\Authentication\Exception\AuthenticationFailedException;
use ForumHouse\SelectelStorageApi\Authentication\Exception\AuthenticationRequiredException;
use ForumHouse\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use ForumHouse\SelectelStorageApi\Exception\UnsupportedResponseFormatException;
use ForumHouse\SelectelStorageApi\Utility\Arr;
use ForumHouse\SelectelStorageApi\Utility\Http\HttpWrapper;
use ForumHouse\SelectelStorageApi\Utility\Response;

/**
 * Class performing authentication with a Selectel endpoint. All data received from endpoint is saved as instance
 * variables
 *
 * @package ForumHouse\SelectelStorageApi\Authentication
 */
class CredentialsAuthentication implements IAuthentication
{
    /**
     * @var string
     */
    private $authUser;

    /**
     * @var string
     */
    private $authKey;

    /**
     * @var string Token received from Selectel authentication endpoint
     */
    protected $authToken;

    /**
     * @var string Storage URL received from Selectel Authentication endpoint
     */
    protected $storageUrl;

    /**
     * @var int For how many seconds token will be available since first authentication
     */
    protected $expireAuthToken;

    /**
     * @var string Authentication URL
     */
    protected $authUrl;


    /**
     * @param string $authUser
     * @param string $authKey
     * @param string $authUrl
     */
    public function __construct($authUser, $authKey, $authUrl = 'https://auth.selcdn.ru')
    {
        $this->authUrl = $authUrl;
        $this->authUser = $authUser;
        $this->authKey = $authKey;
    }


    public function getAuthToken()
    {
        $this->assertAuthenticated();

        return $this->authToken;
    }

    public function getStorageUrl()
    {
        $this->assertAuthenticated();

        return $this->storageUrl;
    }

    public function getExpireAuthToken()
    {
        $this->assertAuthenticated();

        return $this->expireAuthToken;
    }

    /**
     * Performs authentication with a Selectel endpoint and receives all data from it
     *
     * @throws AuthenticationFailedException
     * @throws UnexpectedHttpStatusException
     */
    public function authenticate()
    {
        $client = new HttpWrapper();
        $client->setExpectedHttpCodes([Response::HTTP_NO_CONTENT]);
        $client->addCustomError(Response::HTTP_FORBIDDEN, AuthenticationFailedException::class);
        $client->sendPlainHttpRequest(
            'get',
            $this->authUrl,
            ['X-Auth-User' => $this->authUser, 'X-Auth-Key' => $this->authKey]
        );

        $this->importHeaders($client->getResponseHeaders());
    }

    /**
     * Exports authentication data to be able to reuse it later
     *
     * @return array
     */
    public function exportAuthenticationData()
    {
        $this->assertAuthenticated();

        return array(
            'authToken' => $this->authToken,
            'storageUrl' => $this->storageUrl,
            'expireAuthToken' => $this->expireAuthToken,
        );
    }

    /**
     * Asserts, that we have authenticated
     *
     * @throws AuthenticationRequiredException
     */
    protected function assertAuthenticated()
    {
        if (!$this->authToken) {
            throw new AuthenticationRequiredException;
        }
    }

    /**
     * Imports Selectel response headers into instance variables
     *
     * @param array $headers
     *
     * @throws UnsupportedResponseFormatException
     */
    protected function importHeaders(array $headers)
    {
        $absentKeys = Arr::findAbsent($headers, array('X-Expire-Auth-Token', 'X-Storage-Url', 'X-Auth-Token'));
        if (!empty($absentKeys)) {
            throw new UnsupportedResponseFormatException(
                "Authentication response has the following data absent: ".implode(
                    ', ',
                    $absentKeys
                )
            );
        }

        $this->expireAuthToken = $headers['X-Expire-Auth-Token'][0];
        $this->storageUrl = trim($headers['X-Storage-Url'][0], '/');
        $this->authToken = $headers['X-Auth-Token'][0];
    }
}
