<?php

namespace JWage\APNS;

use ErrorException;

class SocketClient
{
    const PAYLOAD_MAX_BYTES = 256;

    /**
     * @var \JWage\APNS\Certificate
     */
    private $certificate;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var Resource
     */
    private $apnsResource;

    /**
     * @var integer
     */
    private $error;

    /**
     * @var string
     */
    private $errorString;

    /**
     * Construct.
     *
     * @param \JWage\APNS\Certificate $certificate
     * @param string $host
     * @param string $port
     */
    public function __construct(Certificate $certificate, $host, $port)
    {
        $this->certificate = $certificate;
        $this->host = $host;
        $this->port = $port;
    }

    public function __destruct()
    {
        if (is_resource($this->apnsResource)) {
            fclose($this->apnsResource);
        }
    }

    /**
     * Writes a binary message to apns.
     *
     * @param string $binaryMessage
     * @return integer|false Returns the number of bytes written, or FALSE on error.
     */
    public function write($binaryMessage)
    {
        if (strlen($binaryMessage) > self::PAYLOAD_MAX_BYTES) {
            throw new \InvalidArgumentException(
                sprintf('The maximum size allowed for a notification payload is %s bytes; Apple Push Notification Service refuses any notification that exceeds this limit.', self::PAYLOAD_MAX_BYTES)
            );
        }

        return fwrite($this->getApnsResource(), $binaryMessage);
    }

    /**
     * @return Resource
     */
    protected function getApnsResource()
    {
        if (!is_resource($this->apnsResource)) {
            $this->apnsResource = $this->createStreamClient();
        }

        return $this->apnsResource;
    }

    /**
     * @return Resource
     */
    protected function createStreamContext()
    {
        $streamContext = stream_context_create();
        stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->certificate->writeToTmp());

        return $streamContext;
    }

    /**
     * @return Resource
     */
    protected function createStreamClient()
    {
        $address = $this->getSocketAddress();

        $client = stream_socket_client(
            $address,
            $this->error,
            $this->errorString,
            2,
            STREAM_CLIENT_CONNECT,
            $this->createStreamContext()
        );

        if (!$client) {
            throw new ErrorException(
                sprintf('Failed to create stream socket client to "%s". %s', $address, $this->errorString), $this->error
            );
        }

        return $client;
    }

    /**
     * @return string $socketAddress
     */
    protected function getSocketAddress()
    {
        return sprintf('ssl://%s:%s', $this->host, $this->port);
    }
}
