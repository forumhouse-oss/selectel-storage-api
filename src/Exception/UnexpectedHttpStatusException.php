<?php

namespace ForumHouse\SelectelStorageApi\Exception;

/**
 * Unexpected HTTP status exception
 *
 * @package ForumHouse\SelectelStorageApi\Exception
 */
class UnexpectedHttpStatusException extends AbstractSelectelStorageException
{
    /**
     * Constructor
     *
     * @param int $statusCode    HTTP status code
     * @param int $reasonMessage HTTP Reason message, describing status code
     */
    public function __construct($statusCode, $reasonMessage)
    {
        parent::__construct(sprintf("Unexpected HTTP status [%s]: %s", $statusCode, $reasonMessage), $statusCode);
    }
}
 