<?php namespace FHTeam\SelectelStorageApi\Exception;

class FileNotExistsException extends AbstractSelectelStorageException
{

    public function __construct($filename)
    {
        parent::__construct("File '$filename' does not exist");
    }
}
