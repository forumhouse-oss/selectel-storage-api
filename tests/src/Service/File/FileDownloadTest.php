<?php namespace FHTeam\SelectelStorageApi\Test\Service\File;

use FHTeam\SelectelStorageApi\Objects\File;
use FHTeam\SelectelStorageApi\Service\File\FileDownloadService;
use FHTeam\SelectelStorageApi\Service\File\FileUploadService;
use FHTeam\SelectelStorageApi\Test\Service\ServiceTestBase;

/**
 * Class FileDownloadTest
 *
 * @package FHTeam\SelectelStorageApi\Test\Service
 */
class FileDownloadTest extends ServiceTestBase
{
    /**
     * @var FileUploadService
     */
    protected $fileUploadService;

    /**
     * @var FileDownloadService
     */
    protected $fileDownloadService;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fileUploadService = new FileUploadService($this->auth);
        $this->fileDownloadService = new FileDownloadService($this->auth);
    }

    /**
     *
     */
    public function testDownloadFile()
    {
        $fileUp = new File('test.txt');
        $fileUp->setLocalName($this->testFileName);
        $fileUp->setContentType();
        $fileUp->setSize();

        $this->fileUploadService->uploadFile($this->container, $fileUp);
        $fileDown = new File('test.txt');

        $tmpFileName = tempnam(sys_get_temp_dir(), 'selectel-storage-api-test');
        $fileDown->setLocalName($tmpFileName);
        $this->fileDownloadService->downloadFile($this->container, $fileDown);

        $fileUpContents = file_get_contents($this->testFileName);
        $fileDownContents = file_get_contents($tmpFileName);

        if ($fileDownContents !== $fileUpContents) {
            throw new \Exception("Downloaded file contents is not equal to an uploaded one");
        }
    }

    /**
     *
     */
    public function testDownloadFiles()
    {
        $fileUp = new File('test.txt');
        $fileUp->setLocalName($this->testFileName);
        $fileUp->setContentType();
        $fileUp->setSize();

        $this->fileUploadService->uploadFile($this->container, $fileUp);
        $fileDown = new File('test.txt');

        $tmpFileName = tempnam(sys_get_temp_dir(), 'selectel-storage-api-test');
        $fileDown->setLocalName($tmpFileName);
        $this->fileDownloadService->downloadFiles($this->container, [$fileDown]);

        $fileUpContents = file_get_contents($this->testFileName);
        $fileDownContents = file_get_contents($tmpFileName);

        $this->assertSame($fileUpContents, $fileDownContents);
    }
}
