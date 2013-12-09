<?php

namespace JWage\APNS\Tests;

use PHPUnit_Framework_TestCase;
use JWage\APNS\APNSMessage;
use JWage\APNS\Payload;

class APNSMessageTest extends PHPUnit_Framework_TestCase
{
    private $deviceToken;
    private $payload;
    private $apnsMessage;

    protected function setUp()
    {
        $this->deviceToken = '97213C2CA2146AF258B098611394FD6943FA730FF65E6797A85D3A0DC713A84C';
        $this->payload = new Payload('title', 'body', 'deep link');
        $this->apnsMessage = new APNSMessage($this->deviceToken, $this->payload);
    }

    public function testGetBinaryMessage()
    {
        $expectedBinaryMessage = $this->createBinaryMessage(
            $this->deviceToken, $this->payload->getPayload()
        );

        $binaryMessage = $this->apnsMessage->getBinaryMessage();
        $this->assertEquals($expectedBinaryMessage, $binaryMessage);
    }

    private function createBinaryMessage($deviceToken, array $payload)
    {
        $encodedPayload = json_encode($payload);

        return chr(0).
               chr(0).
               chr(32).
               pack('H*', $deviceToken).
               chr(0).chr(strlen($encodedPayload)).
               $encodedPayload;
    }
}
