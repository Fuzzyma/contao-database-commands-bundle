<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{

    protected function configure()
    {

        $this
            ->setName('contao:setup')
            ->setDescription('Accepts license, updates database and creates admin user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $command = $this->getApplication()->find('contao:license');

        if ($command->run(new ArrayInput([]), $output)) {
            return;
        }

        $command = $this->getApplication()->find('doctrine:database:create');

        if ($command->run(new ArrayInput(["--if-not-exists" => true]), $output)) {
            return;
        }

        $command = $this->getApplication()->find('contao:database:update');

        if ($command->run(new ArrayInput([]), $output)) {
            return;
        }

        $command = $this->getApplication()->find('contao:database:addAdmin');

        if ($command->run(new ArrayInput([]), $output)) {
            return;
        }

    }

}