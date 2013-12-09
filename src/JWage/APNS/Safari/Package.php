<?php

namespace JWage\APNS\Safari;

use JWage\APNS\Certificate;
use ErrorException;
use ZipArchive;

class Package
{
    /**
     * @var array
     */
    public static $packageFiles = array(
        'icon.iconset/icon_16x16.png',
        'icon.iconset/icon_16x16@2x.png',
        'icon.iconset/icon_32x32.png',
        'icon.iconset/icon_32x32@2x.png',
        'icon.iconset/icon_128x128.png',
        'icon.iconset/icon_128x128@2x.png',
        'website.json'
    );

    /**
     * @var \JWage\APNS\Certificate
     */
    private $certificate;

    /**
     * @var string
     */
    private $basePushPackagePath;

    /**
     * @var string
     */
    private $packageDir;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $zipPath;

    /**
     * @var boolean
     */
    private $isWritten = false;

    /**
     * @param \JWage\APNS\Certificate $certificate
     * @param string $basePushPackagePath
     * @param string $packageDir
     * @param string $userId
     * @param string $host
     */
    public function __construct(
        Certificate $certificate,
        $basePushPackagePath,
        $packageDir,
        $userId,
        $host
    )
    {
        $this->certificate = $certificate;
        $this->basePushPackagePath = $basePushPackagePath;
        $this->packageDir = $packageDir;
        $this->userId = $userId;
        $this->host = $host;
        $this->zipPath = sprintf('%s.zip', $this->packageDir);
    }

    /**
     * Gets path to the zip package.
     *
     * @return string $zipPath
     */
    public function getZipPath()
    {
        if ($this->isWritten === false) {
            $this->generatePackage();
        }

        return $this->zipPath;
    }

    private function generatePackage()
    {
        $this->isWritten = true;

        if (!is_dir($this->packageDir)) {
            mkdir($this->packageDir);
        }

        $this->copyPackageFiles();
        $this->createManifest();
        $this->createSignature();

        $zip = $this->createZipArchive();

        if (!$zip->open($this->zipPath, ZipArchive::CREATE)) {
            throw new ErrorException(sprintf('Could not open package "%s"', $this->zipPath));
        }

        $packageFiles = self::$packageFiles;
        $packageFiles[] = 'manifest.json';
        $packageFiles[] = 'signature';

        foreach ($packageFiles as $packageFile) {
            $filePath = sprintf('%s/%s', $this->packageDir, $packageFile);

            if (!file_exists($filePath)) {
                throw new ErrorException(sprintf('File does not exist "%s"', $filePath));
            }

            $zip->addFile($filePath, $packageFile);
        }

        if (false === $zip->close()) {
            throw new ErrorException(sprintf('Could not save package "%s"', $this->zipPath));
        }
    }

    private function copyPackageFiles()
    {
        mkdir($this->packageDir . '/icon.iconset');

        foreach (Package::$packageFiles as $rawFile) {
            $filePath = sprintf('%s/%s', $this->packageDir, $rawFile);

            copy(sprintf('%s/%s', $this->basePushPackagePath, $rawFile), $filePath);

            if ($rawFile === 'website.json') {
                $websiteJson = file_get_contents($filePath);
                $websiteJson = str_replace('{{ userId }}', $this->userId, $websiteJson);
                $websiteJson = str_replace('{{ host }}', $this->host, $websiteJson);
                file_put_contents($filePath, $websiteJson);
            }
        }
    }

    private function createManifest()
    {
        return $this->createPackageManifest()->createManifest();
    }

    private function createSignature()
    {
        return $this->createPackageSigner()->createPackageSignature(
            $this->certificate, $this->packageDir
        );
    }

    protected function createPackageSigner()
    {
        return new PackageSigner();
    }

    protected function createPackageManifest()
    {
        return new PackageManifest($this->packageDir);
    }

    protected function createZipArchive()
    {
        return new ZipArchive();
    }
}
