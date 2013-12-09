<?php

namespace JWage\APNS\Tests;

use PHPUnit_Framework_TestCase;
use JWage\APNS\Certificate;

class CertificateTest extends PHPUnit_Framework_TestCase
{
    public function testGetCertificateString()
    {
        $certificate = new Certificate(null);
        $this->assertEquals('', $certificate->getCertificateString());

        $certificate = new Certificate('test');
        $this->assertEquals('test', $certificate->getCertificateString());
    }

    public function testPassword()
    {
        $certificate = new Certificate(null);
        $this->assertNull($certificate->getPassword());

        $certificate = new Certificate(null, 'password');
        $this->assertEquals('password', $certificate->getPassword());
    }

    public function testToString()
    {
        $certificate = new Certificate('test');
        $this->assertEquals('test', (string) $certificate);
    }

    public function testWriteTo()
    {
        $path = '/tmp/certificate_test_'.time();
        $certificate = new Certificate('test');
        $certificate->writeTo($path);
        $this->assertFileExists($path);
        $this->assertEquals('test', file_get_contents($path));
    }

    public function testWriteToTmp()
    {
        $certificate = new Certificate('test');
        $path = $certificate->writeToTmp();
        $this->assertFileExists($path);
        $this->assertEquals('test', file_get_contents($path));
    }
}
