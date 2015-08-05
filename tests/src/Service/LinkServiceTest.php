<?php namespace FHTeam\SelectelStorageApi\Test\Service;

use Exception;
use FHTeam\SelectelStorageApi\Authentication\Exception\AuthenticationFailedException;
use FHTeam\SelectelStorageApi\Exception\FileCrcFailedException;
use FHTeam\SelectelStorageApi\Exception\UnexpectedHttpStatusException;
use FHTeam\SelectelStorageApi\Objects\File;
use FHTeam\SelectelStorageApi\Service\File\FileUploadService;
use FHTeam\SelectelStorageApi\Service\LinkService;
use GuzzleHttp\Client;

class LinkServiceTest extends ServiceTestBase
{
    /**
     * @var FileUploadService
     */
    protected $fileUploadService;

    /**
     * @var LinkService
     */
    protected $linkService;

    /**
     * @throws AuthenticationFailedException
     * @throws UnexpectedHttpStatusException
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fileUploadService = new FileUploadService($this->auth);
        $this->linkService = new LinkService($this->auth);
    }

    /**
     * @throws FileCrcFailedException
     * @throws UnexpectedHttpStatusException
     * @throws Exception
     */
    public function testSignUrl()
    {
        $file = new File('test.txt');
        $file->setLocalName($this->testFileName);
        $file->setContentType();
        $file->setSize();

        $this->fileUploadService->uploadFile($this->container, $file);

        // WARNING: uncommenting this line will set secret key across ALL containers on your account
        // during next tests run
        //$this->service->setAccountSecretKey($this->containerSecretKey);

        $signedUrl = (new LinkService())->signFileDownloadLink(
            $this->containerUrl.'/'.$file->getServerName(),
            time() + 600,
            $this->containerSecretKey
        );

        //No exception is expected here
        (new Client())->get($signedUrl);
    }
}
