<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle;

use Fuzzyma\Contao\DatabaseCommandsBundle\Command\AcceptLicenseCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoDatabaseCommandsBundle extends Bundle
{

    public function registerCommands(Application $application)
    {
        // stop the unneeded search here!!
    }

}
