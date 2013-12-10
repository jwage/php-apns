<?php

namespace JWage\APNS\Safari;

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
    private $zipPath;

    /**
     * @param string $packageDir
     * @param string $userId
     */
    public function __construct($packageDir, $userId)
    {
        $this->packageDir = $packageDir;
        $this->userId = $userId;
        $this->zipPath = sprintf('%s.zip', $packageDir);
    }

    /**
     * Gets path to the zip package directory.
     *
     * @return string $packageDir
     */
    public function getPackageDir()
    {
        return $this->packageDir;
    }

    /**
     * Gets the user id this package is for.
     *
     * @return string $userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Gets path to the zip package.
     *
     * @return string $zipPath
     */
    public function getZipPath()
    {
        return $this->zipPath;
    }
}
