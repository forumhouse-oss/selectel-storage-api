<?php namespace FHTeam\SelectelStorageApi\Test\Service;

use FHTeam\SelectelStorageApi\Authentication\Exception\AuthenticationFailedException;
use FHTeam\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use FHTeam\SelectelStorageApi\Objects\File;
use FHTeam\SelectelStorageApi\Service\File\FileUploadService;
use GuzzleHttp\Client;

/**
 * Class FileUploadTest
 *
 * @package FHTeam\SelectelStorageApi\Test\Service
 */
class FileUploadTest extends ServiceTestBase
{
    /**
     * @var FileUploadService
     */
    protected $fileUploadService;

    /**
     * @throws AuthenticationFailedException
     * @throws UnexpectedHttpStatusException
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fileUploadService = new FileUploadService($this->auth);
    }

    /**
     *
     */
    public function testUploadFile()
    {
        $file = new File('test.txt');
        $file->setLocalName($this->testFileName);
        $file->setContentType();
        $file->setSize();

        $this->fileUploadService->uploadFile($this->container, $file);
        (new Client())->get($this->containerUrl.'/'.$file->getServerName());
    }

    /**
     *
     */
    public function testUploadFiles()
    {
        $file = new File('test.txt');
        $file->setLocalName($this->testFileName);
        $file->setContentType();
        $file->setSize();

        $this->fileUploadService->uploadFiles($this->container, [$file], false);
        (new Client())->get($this->containerUrl.'/'.$file->getServerName());
    }
}
