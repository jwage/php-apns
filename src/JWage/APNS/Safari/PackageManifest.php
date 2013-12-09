<?php

namespace JWage\APNS\Safari;

class PackageManifest
{
    /**
     * @var string
     */
    private $packageDir;

    /**
     * Construct.
     *
     * @param string $packageDir
     */
    public function __construct($packageDir)
    {
        $this->packageDir = $packageDir;
    }

    /**
     * Generates a manifest JSON file and returns the path.
     *
     * @return string $manifestJsonPath
     */
    public function createManifest()
    {
        $manifestData = array();
        foreach (Package::$packageFiles as $rawFile) {
            $filePath = sprintf('%s/%s', $this->packageDir, $rawFile);
            $manifestData[$rawFile] = sha1(file_get_contents($filePath));
        }

        $manifestJsonPath = sprintf('%s/manifest.json', $this->packageDir);
        $manifestJson = json_encode((object) $manifestData);

        file_put_contents($manifestJsonPath, $manifestJson);

        return $manifestJsonPath;
    }
}
