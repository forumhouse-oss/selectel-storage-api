<?php namespace ForumHouse\SelectelStorageApi\File;

/**
 * Identifies something, that has server name (URL) on selectel server (URL part after domain and container)
 *
 * @package ForumHouse\SelectelStorageApi\File
 */
interface ServerResourceInterface
{
    /**
     * @return string URL in the selectel container (URL part after domain and container)
     */
    public function getServerName();

    /**
     * @param string $value URL in the selectel container (URL part after domain and container)
     *
     * @return void
     */
    public function setServerName($value);
}
