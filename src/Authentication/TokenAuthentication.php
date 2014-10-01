<?php

namespace ForumHouse\SelectelStorageApi\Authentication;

use ForumHouse\SelectelStorageApi\Utility\Arr;

/**
 * Class does not perform any authentication. Instead, it assumes, authentication was done some time ago and
 * it just reuses it's data
 *
 * @package ForumHouse\SelectelStorageApi\Authentication
 */
class TokenAuthentication implements IAuthentication
{
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
     * Constructor
     *
     * @param string $authToken
     * @param string $storageUrl
     * @param int    $expireAuthToken
     */
    protected function __construct($authToken, $storageUrl, $expireAuthToken)
    {
        $this->authToken = $authToken;
        $this->storageUrl = $storageUrl;
        $this->expireAuthToken = $expireAuthToken;
    }

    /**
     * Creates authentication instance from raw data
     *
     * @param string $authToken       Authentication token
     * @param string $storageUrl      Storage URL
     * @param int    $expireAuthToken Token expiration time in seconds
     *
     * @return static
     */
    public static function createFromData($authToken, $storageUrl, $expireAuthToken)
    {
        return new static($authToken, $storageUrl, $expireAuthToken);
    }

    /**
     * Creates authentication instance from data, previously exported using
     * \ForumHouse\SelectelStorageApi\Authentication\CredentialsAuthentication::exportAuthenticationData
     *
     * @param array $data Previously exported data
     *
     * @return static
     * @throws \Exception
     */
    public static function createFromExported(array $data)
    {
        $absentKeys = Arr::findAbsent($data, array('authToken', 'storageUrl', 'expireAuthToken'));
        if (!empty($absentKeys)) {
            throw new \Exception("The following keys are absent in exported data: " . implode(', ', $absentKeys));
        }
        return new static($data['authToken'], $data['storageUrl'], $data['expireAuthToken']);
    }

    /**
     * @return string Authentication token
     */
    public function getAuthToken()
    {
        return $this->getAuthToken();
    }

    /**
     * @return string Storage URL for performing operations with storage
     */
    public function getStorageUrl()
    {
        return $this->getStorageUrl();
    }

    /**
     * @return int A number of seconds in which token will expire starting from first successful authentication
     */
    public function getExpireAuthToken()
    {
        return $this->expireAuthToken;
    }
}
 