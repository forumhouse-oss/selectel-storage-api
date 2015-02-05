<?php

namespace ForumHouse\SelectelStorageApi\Container;

/**
 * Class representing Selectel storage container
 *
 * @package ForumHouse\SelectelStorageApi
 */
class Container
{
    /**
     * @var string Container name
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @throws \Exception
     * @return string
     */
    public function getName()
    {
        if (empty($this->name)) {
            throw new \Exception("Name for container is not set");
        }
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
