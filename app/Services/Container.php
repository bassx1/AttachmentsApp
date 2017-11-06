<?php

namespace App\Services;


use DI\ContainerBuilder;

class Container
{

    public static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            $builder = new ContainerBuilder();
            $builder->addDefinitions(configPath('container.php'));
            $container = $builder->build();

            self::$instance = $container;
        }

        return self::$instance;
    }


}