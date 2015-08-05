<?php namespace FHTeam\SelectelStorageApi\Test\Service;

use FHTeam\SelectelStorageApi\Authentication\CredentialsAuthentication;
use FHTeam\SelectelStorageApi\Authentication\Exception\AuthenticationFailedException;
use FHTeam\SelectelStorageApi\Authentication\IAuthentication;
use FHTeam\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use FHTeam\SelectelStorageApi\Objects\Container;
use FHTeam\SelectelStorageApi\Objects\File;
use FHTeam\SelectelStorageApi\Service\File\FileDeleteService;
use PHPUnit_Framework_TestCase;

/**
 * Class AbstractServiceTest
 *
 * @package FHTeam\SelectelStorageApi\Test\Service
 */
class ServiceTestBase extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $testFileName;

    /**
     * @var IAuthentication
     */
    protected $auth;

    /**
     * @var \FHTeam\SelectelStorageApi\Objects\Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $containerUrl;

    /**
     * @var string
     */
    protected $containerSecretKey;

    /**
     * @throws AuthenticationFailedException
     * @throws UnexpectedHttpStatusException
     */
    protected function setUp()
    {
        parent::setUp();

        $config = include(__DIR__.'/../../data/config.php');
        $this->container = new Container($config['container_name']);
        $this->containerUrl = $config['container_url'];
        $this->containerSecretKey = $config['container_secret_key'];

        $this->auth = new CredentialsAuthentication($config['auth_user'], $config['auth_key'], $config['auth_url']);
        $this->auth->authenticate();

        $this->testFileName = __DIR__.'/../../data/test_file.txt';

    }

    protected function tearDown()
    {
        parent::tearDown();

        //Deleting file
        $file = new File($this->testFileName);
        $fileDeletionService = new FileDeleteService($this->auth);
        $fileDeletionService->deleteFile($this->container, $file);
    }
}
