<?php

namespace JWage\APNS;

class Client
{
    /**
     * @var \JWage\APNS\SocketClient
     */
    private $socketClient;

    /**
     * Construct.
     *
     * @param \JWage\APNS\SocketClient $socketClient
     */
    public function __construct(SocketClient $socketClient)
    {
        $this->socketClient = $socketClient;
    }

    /**
     * Send Payload instance to a given device token.
     *
     * @param string $deviceToken The device token to send payload to.
     * @param \JWage\APNS\Payload $payload The payload to send to device token.
     */
    public function sendPayload($deviceToken, Payload $payload)
    {
        return $this->socketClient->write(
            $this->createApnMessage($deviceToken, $payload)->getBinaryMessage()
        );
    }

    /**
     * Creates an APNSMessage instance for the given device token and payload.
     *
     * @param string $deviceToken
     * @param \JWage\APNS\Payload $payload
     * @return \JWage\APNS\APNSMessage
     */
    protected function createApnMessage($deviceToken, Payload $payload)
    {
        return new APNSMessage($deviceToken, $payload);
    }
}
