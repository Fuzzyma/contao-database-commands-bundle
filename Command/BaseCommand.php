<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Command;


use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\InstallationBundle\InstallTool;
use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{

    protected $framework;
    protected $installTool;

    public function __construct(ContaoFramework $framework, InstallTool $installTool)
    {
        parent::__construct();

        $this->framework = $framework;
        $this->installTool = $installTool;
    }

} 