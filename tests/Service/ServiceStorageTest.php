<?php

namespace ForumHouse\SelectelStorageApi\Test\ServiceStorage;

use ForumHouse\SelectelStorageApi\Authentication\CredentialsAuthentication;
use ForumHouse\SelectelStorageApi\Container\Container;
use ForumHouse\SelectelStorageApi\File\File;
use ForumHouse\SelectelStorageApi\Service\StorageService;

class ServiceStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \ForumHouse\SelectelStorageApi\Authentication\Exception\AuthenticationFailedException
     * @throws \ForumHouse\SelectelStorageApi\Exception\UnexpectedHttpStatusException
     * @throws \ForumHouse\SelectelStorageApi\File\Exception\CrcFailedException
     */
    public function testUploadFile()
    {
        $config = include(__DIR__ . '/../data/config.php');
        $container = new Container($config['auth_container']);

        $auth = new CredentialsAuthentication($config['auth_user'], $config['auth_key'], $config['auth_url']);
        $auth->authenticate();

        $file = new File('test.txt');
        $file->setLocalName(__DIR__ . '/../data/config.php');
        $file->setSize();

        $service = new StorageService($auth);
        $service->uploadFile($container, $file);
    }

    /**
     * @depends testUploadFile
     * @throws \ForumHouse\SelectelStorageApi\Authentication\Exception\AuthenticationFailedException
     * @throws \ForumHouse\SelectelStorageApi\Exception\UnexpectedHttpStatusException
     */
    public function testDeleteFile()
    {
        $config = include(__DIR__ . '/../data/config.php');
        $container = new Container($config['auth_container']);

        $file = new File('test.txt');

        $auth = new CredentialsAuthentication($config['auth_user'], $config['auth_key'], $config['auth_url']);
        $auth->authenticate();

        $service = new StorageService($auth);
        $service->deleteFile($container, $file);
    }
}
