<?php

namespace ForumHouse\SelectelStorageApi\File;

use ForumHouse\SelectelStorageApi\Exception\UnexpectedError;
use ForumHouse\SelectelStorageApi\File\Exception\FileNotExistsException;
use ForumHouse\SelectelStorageApi\File\Exception\FileUnreadableException;
use ForumHouse\SelectelStorageApi\Utility\FS;

/**
 * Object, representing a file in a storage
 *
 * @package ForumHouse\SelectelStorageApi
 */
class File
{
    /**
     * @var string Name of the file in the container
     */
    protected $serverName;

    /**
     * @var string Name of the file in the filesystem
     */
    protected $localName;

    /**
     * @var string[] Selectel headers for file
     */
    protected $headers;

    /**
     * @param string $serverName Name of the file in the container
     */
    public function __construct($serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * @param string $containerName
     *
     * @return $this
     */
    public function setServerName($containerName)
    {
        $this->serverName = $containerName;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->headers['Content-Length'];
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size = null)
    {
        if (empty($size)) {
            $this->assertFileExists();
            $size = filesize($this->localName);
        }
        $this->headers['Content-Length'] = $size;
        return $this;
    }

    /**
     * Returns ETag header
     *
     * @return string
     */
    public function getMd5()
    {
        return $this->headers['ETag'];
    }

    /**
     * Sets ETag header
     *
     * @param string $value
     *
     * @return $this
     * @throws FileNotExistsException
     */
    public function setMd5($value = null)
    {
        if (empty($value)) {
            $this->assertFileExists();
            $value = md5_file($this->localName);
        }
        $this->headers['ETag'] = $value;
        return $this;
    }

    /**
     * Gets file content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->headers['Content-Type'];
    }

    /**
     * Sets file content type
     *
     * @param string $contentType Content type to set. If not provided - it will be guessed from $this->localName
     *
     * @return $this
     */
    public function setContentType($contentType = null)
    {
        if (empty($contentType)) {
            $this->assertFileExists();
            $contentType = FS::getFileMimeType($this->localName);
        }
        $this->headers['Content-Type'] = $contentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->headers['Content-Disposition'];
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setContentDisposition($value)
    {
        $this->headers['Content-Disposition'] = $value;
        return $this;
    }

    /**
     * Gets http file headers for a file
     *
     * @return \string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Sets http file headers for a file
     *
     * @param string[] $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocalName()
    {
        return $this->localName;
    }

    /**
     * @param string $localName
     *
     * @return $this
     */
    public function setLocalName($localName)
    {
        $this->localName = $localName;
        return $this;
    }

    /**
     * Opens file and returns a handle to it
     *
     * @param string $mode File open mode (see fopen for details)
     *
     * @return resource
     * @throws FileNotExistsException
     * @throws FileUnreadableException
     * @throws UnexpectedError
     */
    public function openLocal($mode)
    {
        $this->assertFileExists();
        $this->assertFileReadable();

        $handle = fopen($this->localName, $mode);
        if (!$handle) {
            throw new UnexpectedError("Cannot open file '{$this->localName}' due to unknown error");
        }

        return $handle;
    }

    /**
     * Asserts that file exists
     *
     * @throws FileNotExistsException
     */
    protected function assertFileExists()
    {
        if (!file_exists($this->localName)) {
            throw new FileNotExistsException($this->localName);
        }

    }

    /**
     * Asserts that file is readable
     *
     * @throws FileNotExistsException
     */
    protected function assertFileReadable()
    {
        if (!is_readable($this->localName)) {
            throw new FileUnreadableException($this->localName);
        }
    }
}
