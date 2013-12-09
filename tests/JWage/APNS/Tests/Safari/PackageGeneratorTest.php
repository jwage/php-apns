<?php

namespace JWage\APNS\Tests\Safari;

use JWage\APNS\Certificate;
use JWage\APNS\Safari\Package;
use JWage\APNS\Safari\PackageGenerator;
use JWage\APNS\Safari\PackageSigner;
use PHPUnit_Framework_TestCase;
use ZipArchive;

class PackageGeneratorTest extends PHPUnit_Framework_TestCase
{
    private $basePushPackagePath;
    private $certificate;
    private $packageGenerator;

    protected function setUp()
    {
        $this->basePushPackagePath = realpath(__DIR__.'/../../../../../data/safariPushPackage.base');

        $this->certificate = new Certificate('', 'password');
        $this->packageGenerator = new PackageGeneratorStub(
            $this->certificate, $this->basePushPackagePath, 'host.com'
        );
    }

    public function testCreatePackageForUser()
    {
        $package = $this->packageGenerator->createPushPackageForUser('userId');
        $this->assertInstanceOf('JWage\APNS\Safari\Package', $package);
        $this->assertTrue(file_exists($package->getZipPath()));

        $zip = new ZipArchive();
        $res = $zip->open($package->getZipPath());
        if ($res === true) {
            $extractTo = sprintf('%s/extract_path', sys_get_temp_dir());
            $zip->extractTo($extractTo);
            $zip->close();

            $files = glob($extractTo.'/*');
            $this->assertEquals(4, count($files));

            $iconPath = sprintf('%s/icon.iconset', $extractTo);
            $icons = glob(sprintf('%s/*.png', $iconPath));
            $this->assertEquals(6, count($icons));

            $manifestJsonPath = sprintf('%s/manifest.json', $extractTo);
            $expectedManifest = array (
                'icon.iconset/icon_16x16.png' => '6b192031f23d78db69155261257590cb81b1a6d7',
                'icon.iconset/icon_16x16@2x.png' => '8de653dd0a03f2300f9756c79b0c8bce3abd922b',
                'icon.iconset/icon_32x32.png' => 'a604fd29a5a7bce5d73fe08a75793e8903172c3f',
                'icon.iconset/icon_32x32@2x.png' => 'da343420793428ad803d7ae435e76e3293e60f21',
                'icon.iconset/icon_128x128.png' => 'c958eb6f34a5f6455d2f4b3c85b3bcde30208b4e',
                'icon.iconset/icon_128x128@2x.png' => '529d000f332ad65d284db541a7b5955fa03fb9e7',
                'website.json' => 'bcafd546fd3e1ea7e94c8ccef3fbb089117a31d3',
            );
            $this->assertEquals(json_encode($expectedManifest), file_get_contents($manifestJsonPath));

            $signaturePath = sprintf('%s/signature', $extractTo);
            $this->assertEquals('test signature', file_get_contents($signaturePath));

            $expectedWebsiteJson =
'{
    "websiteName": "WebsiteName",
    "websitePushID": "web.com.domain",
    "allowedDomains": ["http://host.com", "https://host.com"],
    "urlFormatString": "http://host.com/%@",
    "authenticationToken": "userId",
    "webServiceURL": "https://host.com/safari_push_notifications/userId"
}
';

            $websiteJsonPath = sprintf('%s/website.json', $extractTo);
            $this->assertEquals($expectedWebsiteJson, file_get_contents($websiteJsonPath));
        } else {
            $this->fail('Could not extract zip package');
        }
    }
}

class PackageStub extends Package
{
    protected function createPackageSigner()
    {
        return new PackageSignerStub();
    }
}

class PackageSignerStub extends PackageSigner
{
    public function createPackageSignature(Certificate $certificate, $packageDir)
    {
        $signaturePath = sprintf('%s/signature', $packageDir);
        file_put_contents($signaturePath, 'test signature');
    }
}

class PackageGeneratorStub extends PackageGenerator
{
    protected function createPackage($packageDir, $userId)
    {
        return new PackageStub(
            $this->certificate,
            $this->basePushPackagePath,
            $packageDir,
            $userId,
            $this->host
        );
    }
}
