<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Fuzzyma\Contao\DatabaseCommandsBundle\ContaoDatabaseCommandsBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoDatabaseCommandsBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
