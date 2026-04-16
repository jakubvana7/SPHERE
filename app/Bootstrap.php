<?php
declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;

class Bootstrap
{
    public static function boot(): Configurator
    {
        $appDir = dirname(__DIR__);

        $configurator = new Configurator;
        $configurator->setDebugMode(true);
        $configurator->enableTracy($appDir . '/log');
        $configurator->setTempDirectory($appDir . '/temp');

        $configurator->createRobotLoader()
            ->addDirectory($appDir . '/app')
            ->register();

        $configurator->addDynamicParameters([
            'env' => array_merge([
                'DB_HOST'        => '127.0.0.1',
                'DB_NAME'        => 'vanaj',
                'DB_USER'        => 'root',
                'DB_PASSWORD'    => '',
                'ADMIN_PASSWORD' => 'admin123',
            ], getenv()),
        ]);

        $configurator->addConfig($appDir . '/config/common.neon');

        $localConfig = $appDir . '/config/local.neon';
        if (is_file($localConfig)) {
            $configurator->addConfig($localConfig);
        }

        return $configurator;
    }
}
