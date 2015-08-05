<?php namespace FHTeam\SelectelStorageApi\Test\Service;

use Exception;
use FHTeam\SelectelStorageApi\Authentication\Exception\AuthenticationFailedException;
use FHTeam\SelectelStorageApi\Exception\FileCrcFailedException;
use FHTeam\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use FHTeam\SelectelStorageApi\Objects\File;
use FHTeam\SelectelStorageApi\Objects\SymLink;
use FHTeam\SelectelStorageApi\Service\File\FileUploadService;
use FHTeam\SelectelStorageApi\Service\SymLinkService;

class SymLinkTest extends ServiceTestBase
{
    /**
     * @var FileUploadService
     */
    protected $fileUploadService;

    /**
     * @var SymLinkService
     */
    protected $symLinkService;

    /**
     * @throws AuthenticationFailedException
     * @throws UnexpectedHttpStatusException
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fileUploadService = new FileUploadService($this->auth);
        $this->symLinkService = new SymLinkService($this->auth);
    }

    /**
     * @throws FileCrcFailedException
     * @throws UnexpectedHttpStatusException
     * @throws Exception
     */
    public function testCreateSymlink()
    {
        $file = new File('test.txt');
        $file->setLocalName($this->testFileName);
        $file->setContentType();
        $file->setSize();

        $this->fileUploadService->uploadFile($this->container, $file);

        $link = new SymLink();
        $link->setType(SymLink::TYPE_ONETIME);
        $link->setServerName($this->testFileName);
        $this->symLinkService->createSymlink($this->container, $link);

        //TODO: validate file is downloadable using this symlink
    }

    /**
     * @throws FileCrcFailedException
     * @throws UnexpectedHttpStatusException
     * @throws Exception
     */
    public function testCreateSymlinks()
    {
        $file = new File('test.txt');
        $file->setLocalName($this->testFileName);
        $file->setContentType();
        $file->setSize();

        $this->fileUploadService->uploadFile($this->container, $file);

        $link = new SymLink();
        $link->setType(SymLink::TYPE_ONETIME);
        $link->setServerName($this->testFileName);
        $this->symLinkService->createSymlinks($this->container, [$link]);

        //TODO: validate file is downloadable using this symlink
    }
}
