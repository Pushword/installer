<?php

namespace Pushword\Installer;

use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\Installer\PackageEvent;
use Composer\Script\Event;

class PostInstall
{
    public static function postPackageInstall(PackageEvent $event)
    {
        /** @var InstallOperation $operation */
        $operation = $event->getOperation();

        if ('pushword/core' != $operation->getPackage()->getName()) {
            return;
        }

        exec('sed -i -e "s/parameters:/parameters:\n    locale: \'fr\'\n    database: \'%env(resolve:DATABASE_URL)%\'/" config/services.yaml');

        exec('sed -i -e "/Pushword\\\\\Core\\\\\PushwordCoreBundle::class => \[\'all\' => true\],/d" config/bundles.php');
        exec('sed -i -e "s/return \[/return \[\n    Pushword\\\\\Core\\\\\PushwordCoreBundle::class => \[\'all\' => true\],/" config/bundles.php');
    }

    public static function beforeCacheClear() // todo rename it
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
}
