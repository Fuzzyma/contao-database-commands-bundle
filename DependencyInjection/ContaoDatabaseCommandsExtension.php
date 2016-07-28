<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class ContaoDatabaseCommandsExtension extends Extension
{


    public function load(array $configs, ContainerBuilder $container)
    {

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('commands.yml');

    }

    public function getAlias()
    {
        return 'contao_database_commands';
    }
}
