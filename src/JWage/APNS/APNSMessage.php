<?php

namespace JWage\APNS;

/**
 * APNSMessage class represents an individual message
 * to a device token with a Payload to deliver.
 */
class APNSMessage
{
    /**
     * @var string
     */
    private $deviceToken;

    /**
     * @var \JWage\APNS\Payload
     */
    private $payload;

    /**
     * Construct.
     *
     * @param string $deviceToken
     * @param \JWage\APNS\Payload $payload
     */
    public function __construct($deviceToken, Payload $payload)
    {
        $this->deviceToken = $deviceToken;
        $this->payload = $payload;
    }

    /**
     * Returns a binary message that the Apple Push Notification Service understands.
     *
     * @return string $binaryMessage
     */
    public function getBinaryMessage()
    {
        $encodedPayload = $this->jsonEncode($this->payload->getPayload());

        return chr(0).
               chr(0).
               chr(32).
               pack('H*', $this->deviceToken).
               chr(0).chr(strlen($encodedPayload)).
               $encodedPayload;
    }

    /**
     * @param array $payload
     * @return string $payloadJson
     */
    private function jsonEncode(array $payload)
    {
        return json_encode($payload);
    }
}
