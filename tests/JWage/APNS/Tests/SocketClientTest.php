<?php

namespace JWage\APNS\Tests;

use PHPUnit_Framework_TestCase;
use JWage\APNS\Certificate;
use JWage\APNS\SocketClient;

class SocketClientTest extends PHPUnit_Framework_TestCase
{
    private $testPath;
    private $certificate;
    private $socketClient;

    protected function setUp()
    {
        $this->testPath = tempnam(sys_get_temp_dir(), 'tmp_');
        $this->certificate = new Certificate('');
        $this->socketClient = new SocketClientStub($this->certificate, 'host', 1234);
        $this->socketClient->setTestPath($this->testPath);
    }

    public function testWrite()
    {
        $this->socketClient->write('test');
        $this->assertEquals('test', file_get_contents($this->testPath));
    }

    public function testCreateStreamContext()
    {
        $streamContext = $this->socketClient->getTestCreateStreamContext();
        $this->assertTrue(is_resource($streamContext));
    }

    public function testGetSocketAddress()
    {
        $this->assertEquals('ssl://host:1234', $this->socketClient->getTestSocketAddress());
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage Failed to create stream socket client to "ssl://somethingthatdoesnotexist:100". php_network_getaddresses: getaddrinfo failed: Name or service not known
     */
    public function testConnectThrowsException()
    {
        $socketClient = new SocketClient($this->certificate, 'somethingthatdoesnotexist', 100);
        $socketClient->write('test');
    }
}

class SocketClientStub extends SocketClient
{
    protected $testPath;

    public function setTestPath($testPath)
    {
        $this->testPath = $testPath;
    }

    public function getTestCreateStreamContext()
    {
        return parent::createStreamContext();
    }

    public function getTestSocketAddress()
    {
        return parent::getSocketAddress();
    }

    protected function createStreamClient()
    {
        return fopen($this->testPath, 'r+');
    }
}
