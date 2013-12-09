<?php

namespace JWage\APNS\Safari;

use ErrorException;
use JWage\APNS\Certificate;
use RuntimeException;
use ZipArchive;

class PackageGenerator
{
    /**
     * @var \JWage\APNS\Certificate
     */
    protected $certificate;

    /**
     * @var string
     */
    protected $basePushPackagePath;

    /**
     * @var string
     */
    protected $host;

    /**
     * Construct.
     *
     * @param \JWage\APNS\Certificate $certificate
     * @param string $basePushPackagePath
     * @param string $host
     */
    public function __construct(Certificate $certificate, $basePushPackagePath, $host)
    {
        $this->certificate = $certificate;
        $this->basePushPackagePath = $basePushPackagePath;
        $this->host = $host;
    }

    /**
     * Create a safari website push notification package for the given User.
     *
     * @param string $userId User id to create package for.
     * @return JWage\APNS\Safari\Package $package Package instance.
     */
    public function createPushPackageForUser($userId)
    {
        $packageDir = sprintf('/%s/pushPackage%s.%s', sys_get_temp_dir(), time(), $userId);
        return $this->createPackage($packageDir, $userId);
    }

    protected function createPackage($packageDir, $userId)
    {
        return new Package(
            $this->certificate,
            $this->basePushPackagePath,
            $packageDir,
            $userId,
            $this->host
        );
    }
}
