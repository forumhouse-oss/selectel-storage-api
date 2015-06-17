<?php namespace ForumHouse\SelectelStorageApi\File;

use Exception;
use JsonSerializable;

/**
 * Object, representing symlink, that is to be created
 *
 * @package ForumHouse\SelectelStorageApi
 */
class SymLink implements ServerResourceInterface, JsonSerializable
{
    /** Regular symlink */
    const TYPE_SIMPLE = 'x-storage/symlink';

    /** Automatically deleted after being downloaded once */
    const TYPE_ONETIME = 'x-storage/onetime-symlink';

    /** Secure symlink (password protected) */
    const TYPE_SECURE = 'x-storage/symlink+secure';

    /** One-time and password protected */
    const TYPE_ONETIME_SECURE = 'x-storage/onetime-symlink+secure';

    /**
     * @var string[] HTTP headers for this symlink to be sent to server
     */
    protected $headers = [];

    /**
     * @var int Symlink type
     */
    protected $type;

    /**
     * @var string An object, for which we create symlink
     */
    protected $serverName;

    /**
     * @var int Unix timestamp when this symlink should be automatically deleted
     */
    protected $deleteAt;

    /**
     * @var string Password to protect symlink
     */
    protected $password;

    /**
     * @var string An override of content disposition HTTP header, which will be substituted on download
     */
    protected $contentDisposition;

    /**
     * @return array
     * @throws Exception
     */
    public function getHeaders()
    {
        if (!isset($this->headers['Content-Type'])) {
            throw new Exception('You need to set symlink type first!');
        }

        if (!isset($this->headers['X-Object-Meta-Location'])) {
            throw new Exception('You should set symlink serverName first');
        }

        return $this->headers;
    }

    /**
     * @param array $value
     */
    public function setHeaders($value)
    {
        $this->headers = $value;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $value
     */
    public function setType($value)
    {
        $this->type = $value;
        $this->headers['Content-Type'] = $value;
    }

    /**
     * @return mixed
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * @param mixed $value
     */
    public function setServerName($value)
    {
        $this->serverName = $value;
        $this->headers['X-Object-Meta-Location'] = $value;
        if ($this->password) {
            $this->headers['X-Object-Meta-Link-Key'] = sha1($this->password.$value);
        }
    }

    /**
     * @return mixed
     */
    public function getDeleteAt()
    {
        return $this->deleteAt;
    }

    /**
     * @param mixed $value
     */
    public function setDeleteAt($value)
    {
        $this->deleteAt = $value;
        $this->headers['X-Object-Meta-Delete-At'] = $value;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $value
     *
     * @throws Exception
     */
    public function setPassword($value)
    {
        if (!isset($this->headers['X-Object-Meta-Location'])) {
            throw new Exception("You can set password only after server name!");
        }
        $this->password = $value;
        $this->headers['X-Object-Meta-Link-Key'] = sha1($value.$this->headers['X-Object-Meta-Location']);
    }

    /**
     * @return mixed
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    /**
     * @param mixed $value
     */
    public function setContentDisposition($value)
    {
        $this->contentDisposition = $value;
        $this->headers['Content-Disposition'] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *       which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'serverName' => $this->serverName,
            'deleteAt' => $this->deleteAt,
            'password' => $this->password,
            'contentDisposition' => $this->contentDisposition,
        ];
    }
}
