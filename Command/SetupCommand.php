<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Command;

use Contao\CoreBundle\Command\AbstractLockedCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class SetupCommand extends AbstractLockedCommand
{

    protected function configure()
    {

        $this
            ->setName('contao:setup')
            ->setDescription('Accepts license, updates database and creates admin user');
    }

    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {

        $command = $this->getApplication()->find('contao:license');

        if($command->run(new ArrayInput([]), $output)){
            return;
        }

        $command = $this->getApplication()->find('contao:database:update');

        if($command->run(new ArrayInput([]), $output)){
            return;
        }

        $command = $this->getApplication()->find('contao:database:addAdmin');

        if($command->run(new ArrayInput([]), $output)){
            return;
        }

    }

}