<?php namespace ForumHouse\SelectelStorageApi\Test\Authentication;

use ForumHouse\SelectelStorageApi\Authentication\CredentialsAuthentication;
use PHPUnit_Framework_TestCase;

class CredentialsAuthenticationTest extends PHPUnit_Framework_TestCase
{
    public function testAuthenticate()
    {
        $config = include(__DIR__.'/../data/config.php');
        $auth = new CredentialsAuthentication($config['auth_user'], $config['auth_key'], $config['auth_url']);
        $auth->authenticate();
        $this->assertNotEmpty($auth->getAuthToken());
        $this->assertInternalType('string', $auth->getAuthToken());
    }
}
