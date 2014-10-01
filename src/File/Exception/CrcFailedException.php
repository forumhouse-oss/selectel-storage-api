<?php

namespace ForumHouse\SelectelStorageApi\File\Exception;

use ForumHouse\SelectelStorageApi\Exception\AbstractSelectelStorageException;

class CrcFailedException extends AbstractSelectelStorageException
{

    public function __construct($filename)
    {
        parent::__construct("CRC check for file '$filename' failed");
    }
}

 