<?php

namespace Pushword\Installer;

use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\Installer\PackageEvent;
use Exception;
use Symfony\Component\Filesystem\Filesystem;

class PostInstall
{
    public static function postPackageInstall(PackageEvent $event): void
    {
        /** @var InstallOperation $operation */
        $operation = $event->getOperation();

        $installer = 'vendor/'.$operation->getPackage()->getName().'/src/installer.php';
        if (file_exists($installer)) {
            echo 'Executing '.$operation->getPackage()->getName().' installer.'.\chr(10);
            include $installer;
        }
    }

    public static function partialUnflex(): void
    {
        $files = [
            'templates/base.html.twig',
            'config/packages/security.yaml',
            'config/packages/liip_imagine.yaml',
            'config/packages/vich_uploader.yaml',
            'config/packages/sonata_admin.yaml',
            'config/packages/translation.yaml',
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public static function mirror(string $source, string $dest)
    {
        require_once 'vendor/symfony/filesystem/Filesystem.php';
        (new Filesystem())->mirror($source, $dest);
    }

    public static function remove($path)
    {
        require_once 'vendor/symfony/filesystem/Filesystem.php';
        (new Filesystem())->remove($path);
    }

    public static function dumpFile(string $path, string $content)
    {
        require_once 'vendor/symfony/filesystem/Filesystem.php';
        (new Filesystem())->dumpFile($path, $content);
    }

    public static function replace(string $file, string $search, string $replace): void
    {
        $content = file_get_contents($file);
        if (false === $content) {
            throw new Exception('`'.$file.'` not found');
        }

        $content = str_replace($search, $replace, $content);
        file_put_contents($file, $content);
    }

    public static function addOnTop(string $file, string $toAdd): void
    {
        $content = (string) @file_get_contents($file);
        $content = $toAdd.$content;
        self::dumpFile($file, $content);
    }
}
