<?php namespace FHTeam\SelectelStorageApi\Test\Service;

use FHTeam\SelectelStorageApi\Authentication\Exception\AuthenticationFailedException;
use FHTeam\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use FHTeam\SelectelStorageApi\Objects\File;
use FHTeam\SelectelStorageApi\Service\File\FileDeleteService;
use FHTeam\SelectelStorageApi\Service\File\FileUploadService;

class FileDeleteTest extends ServiceTestBase
{
    /**
     * @var FileUploadService
     */
    protected $fileUploadService;

    /**
     * @var FileDeleteService
     */
    protected $fileDeleteService;

    /**
     * @throws AuthenticationFailedException
     * @throws UnexpectedHttpStatusException
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fileUploadService = new FileUploadService($this->auth);
        $this->fileDeleteService = new FileDeleteService($this->auth);
    }

    /**
     * @throws AuthenticationFailedException
     * @throws UnexpectedHttpStatusException
     */
    public function testDeleteFile()
    {
        $file = new File('test.txt');
        $file->setLocalName($this->testFileName);
        $file->setContentType();
        $file->setSize();

        $this->fileUploadService->uploadFile($this->container, $file);

        $file = new File($this->testFileName);

        $this->fileDeleteService->deleteFile($this->container, $file);
    }

    /**
     * @throws AuthenticationFailedException
     * @throws UnexpectedHttpStatusException
     */
    public function testDeleteFiles()
    {
        $file = new File('test.txt');
        $file->setLocalName($this->testFileName);
        $file->setContentType();
        $file->setSize();

        $this->fileUploadService->uploadFile($this->container, $file);

        $file = new File($this->testFileName);

        $this->fileDeleteService->deleteFiles($this->container, [$file]);
    }
}
