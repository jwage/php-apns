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
     * @return \JWage\APNS\Safari\Package $package Package instance.
     */
    public function createPushPackageForUser($userId)
    {
        $packageDir = sprintf('/%s/pushPackage%s.%s', sys_get_temp_dir(), time(), $userId);
        $package = $this->createPackage($packageDir, $userId);

        $this->generatePackage($package);

        return $package;
    }

    private function generatePackage(Package $package)
    {
        $packageDir = $package->getPackageDir();
        $zipPath = $package->getZipPath();

        if (!is_dir($packageDir)) {
            mkdir($packageDir);
        }

        $this->copyPackageFiles($package);
        $this->createPackageManifest($package);
        $this->createPackageSignature($package);

        $zip = $this->createZipArchive();

        if (!$zip->open($zipPath, ZipArchive::CREATE)) {
            throw new ErrorException(sprintf('Could not open package "%s"', $zipPath));
        }

        $packageFiles = Package::$packageFiles;
        $packageFiles[] = 'manifest.json';
        $packageFiles[] = 'signature';

        foreach ($packageFiles as $packageFile) {
            $filePath = sprintf('%s/%s', $packageDir, $packageFile);

            if (!file_exists($filePath)) {
                throw new ErrorException(sprintf('File does not exist "%s"', $filePath));
            }

            $zip->addFile($filePath, $packageFile);
        }

        if (false === $zip->close()) {
            throw new ErrorException(sprintf('Could not save package "%s"', $zipPath));
        }
    }

    private function copyPackageFiles(Package $package)
    {
        $packageDir = $package->getPackageDir();

        mkdir($packageDir . '/icon.iconset');

        foreach (Package::$packageFiles as $rawFile) {
            $filePath = sprintf('%s/%s', $packageDir, $rawFile);

            copy(sprintf('%s/%s', $this->basePushPackagePath, $rawFile), $filePath);

            if ($rawFile === 'website.json') {
                $websiteJson = file_get_contents($filePath);
                $websiteJson = str_replace('{{ userId }}', $package->getUserId(), $websiteJson);
                $websiteJson = str_replace('{{ host }}', $this->host, $websiteJson);
                file_put_contents($filePath, $websiteJson);
            }
        }
    }

    private function createPackageManifest(Package $package)
    {
        return $this->createPackageManifester()->createManifest($package);
    }

    private function createPackageSignature(Package $package)
    {
        return $this->createPackageSigner()->createPackageSignature(
            $this->certificate, $package
        );
    }

    protected function createPackageSigner()
    {
        return new PackageSigner();
    }

    protected function createPackageManifester()
    {
        return new PackageManifester();
    }

    protected function createZipArchive()
    {
        return new ZipArchive();
    }

    protected function createPackage($packageDir, $userId)
    {
        return new Package($packageDir, $userId);
    }
}
