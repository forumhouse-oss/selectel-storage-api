<?php namespace ForumHouse\SelectelStorageApi\File\Exception;

use ForumHouse\SelectelStorageApi\Exception\AbstractSelectelStorageException;

class FileNotExistsException extends AbstractSelectelStorageException
{

    public function __construct($filename)
    {
        parent::__construct("File '$filename' does not exist");
    }
}
