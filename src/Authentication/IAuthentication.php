<?php

namespace ForumHouse\SelectelStorageApi\Authentication;

/**
 * Authentication interface
 */
interface IAuthentication
{
    /**
     * @return string Authentication token
     */
    public function getAuthToken();

    /**
     * @return string Storage URL for performing operations with storage
     */
    public function getStorageUrl();

    /**
     * @return int A number of seconds in which token will expire starting from first successful authentication
     */
    public function getExpireAuthToken();
}
