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
            'env' => getenv(),
        ]);

        $configurator->addConfig($appDir . '/config/common.neon');

        $localConfig = $appDir . '/config/local.neon';
        if (is_file($localConfig)) {
            $configurator->addConfig($localConfig);
        }

        return $configurator;
    }
}
