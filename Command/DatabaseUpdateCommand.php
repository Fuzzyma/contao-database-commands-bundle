<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Command;

use Contao\CoreBundle\Command\AbstractLockedCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class DatabaseUpdateCommand extends AbstractLockedCommand
{

    protected function configure()
    {

        $this
            ->setName('contao:database:update')
            ->setDescription('Updates the database to reflect all changes made in the dca files')
            ->setDefinition(array(
                new InputOption('drop', 'd', InputOption::VALUE_NONE, 'Includes table and column drops')
            ));
    }

    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {

        $drop = !!$input->getOption('drop');

        $this->getContainer()->get('contao.framework')->initialize();
        $this->getContainer()->get('contao.install_tool')->handleRunOnce();

        $installer = $this->getContainer()->get('contao.installer');

        $commands = $installer->getCommands();

        if (!$drop) {
            unset($commands['DROP']);
            unset($commands['ALTER_DROP']);
        }

        foreach ($commands as $category) {
            foreach ($category as $hash => $sql) {
                $output->writeln('<info>Executing Query: "' . $sql . '"</info>');
                $installer->execCommand($hash);
            }
        }

        $output->writeln('<info>Success: Database update complete</info>');

    }

}