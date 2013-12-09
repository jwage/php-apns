<?php

namespace JWage\APNS\Tests;

use PHPUnit_Framework_TestCase;
use JWage\APNS\Payload;
use JWage\APNS\Sender;

class SenderTest extends PHPUnit_Framework_TestCase
{
    private $client;
    private $sender;

    protected function setUp()
    {
        $this->client = $this->getMockBuilder('JWage\APNS\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->sender = new Sender($this->client);
    }

    public function testSend()
    {
        $payload = new Payload('title', 'body', 'deep link');
        $this->client->expects($this->once())
            ->method('sendPayload')
            ->with('device token', $payload);
        $this->sender->send('device token', 'title', 'body', 'deep link');
    }
}
