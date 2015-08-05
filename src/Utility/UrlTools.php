<?php namespace FHTeam\SelectelStorageApi\Utility;

use Exception;
use FHTeam\SelectelStorageApi\Authentication\IAuthentication;
use FHTeam\SelectelStorageApi\Objects\Container;
use FHTeam\SelectelStorageApi\Objects\ServerResourceInterface;

/**
 * Class UrlTools
 *
 * @package ForumHouse\SelectelStorageApi\Utility
 */
class UrlTools
{
    /**
     * @param IAuthentication                                            $authentication
     * @param Container                                                  $container
     * @param \FHTeam\SelectelStorageApi\Objects\ServerResourceInterface $file
     *
     * @return string
     * @throws Exception
     */
    public static function getServerResourceUrl(
        IAuthentication $authentication,
        Container $container,
        ServerResourceInterface $file
    ) {
        return $authentication->getStorageUrl().'/'.$container->getName().'/'.$file->getServerName();
    }
}
