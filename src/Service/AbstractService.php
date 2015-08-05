<?php namespace FHTeam\SelectelStorageApi\Service;

use FHTeam\SelectelStorageApi\Authentication\IAuthentication;

/**
 * Class AbstractService
 *
 * @package ForumHouse\SelectelStorageApi\Service
 */
class AbstractService
{
    /**
     * @var IAuthentication
     */
    protected $authentication;

    /**
     * @param IAuthentication $authentication
     */
    public function __construct(IAuthentication $authentication)
    {
        $this->authentication = $authentication;
    }
}
