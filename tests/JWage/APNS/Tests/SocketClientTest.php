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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The maximum size allowed for a notification payload is 256 bytes; Apple Push Notification Service refuses any notification that exceeds this limit.
     */
    public function testWriteMaxBytes()
    {
        $deviceToken = '97213C2CA2146AF258B098611394FD6943FA730FF65E6797A85D3A0DC713A84C';

        $payload = array(
            'aps' => array(
                'alert' => array(
                    'title' => 'Website',
                    'body' => 'Jonathan H. Wage joined your website. This is a really long push notification body. So long it probably won\'t work! Not long enough! Perfect!',
                ),
                'url-args' => array(
                    'http://google.com',
                ),
            ),
        );

        $encodedPayload = json_encode($payload);

        $payload = chr(0).
               chr(0).
               chr(32).
               pack('H*', $deviceToken).
               chr(0).chr(strlen($encodedPayload)).
               $encodedPayload;

        // write binary string to file
        $path = sprintf('%s/php_apns_test_write_max_bytes', sys_get_temp_dir());
        file_put_contents($path, $payload);

        $this->assertEquals(filesize($path), strlen($payload), 'Compare result of filesize() to strlen()');

        $this->socketClient->write($payload);
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
