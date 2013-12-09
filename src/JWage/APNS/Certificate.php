<?php

namespace JWage\APNS;

class Certificate
{
    /**
     * @var string
     */
    private $certificateString;

    /**
     * @var string
     */
    private $password;

    /**
     * Construct.
     *
     * @param string $certificateString
     * @param string $password
     */
    public function __construct($certificateString, $password = null)
    {
        $this->certificateString =  (string) $certificateString;
        $this->password = $password;
    }

    /**
     * Gets the certificate string.
     *
     * @return string $certificateString
     */
    public function getCertificateString()
    {
        return $this->certificateString;
    }

    /**
     * Gets the certificate password.
     *
     * @return string $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Writes the certificate to the given file path.
     *
     * @param string $path
     */
    public function writeTo($path)
    {
        file_put_contents($path, $this->certificateString);
    }

    /**
     * Writes the certificate to a temporary file and returns the path.
     *
     * @return string $path
     */
    public function writeToTmp()
    {
        $path = tempnam(sys_get_temp_dir(), 'cert_');

        $this->writeTo($path);

        return $path;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->certificateString;
    }
}
