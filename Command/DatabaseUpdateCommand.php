<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\InstallationBundle\Database\Installer;
use Contao\InstallationBundle\InstallTool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class DatabaseUpdateCommand extends BaseCommand
{

    private $installer;

    public function __construct(ContaoFramework $framework, InstallTool $installTool, Installer $installer)
    {
        parent::__construct($framework, $installTool);

        $this->installer = $installer;
    }

    protected function configure()
    {

        $this
            ->setName('contao:database:update')
            ->setDescription('Updates the database to reflect all changes made in the dca files')
            ->setDefinition(array(
                new InputOption('drop', 'd', InputOption::VALUE_NONE, 'Includes table and column drops'),
                new InputOption('dry-run', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_NONE, 'Only print queries. Does not touch database')
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $drop = !!$input->getOption('drop');

        $this->framework->initialize();
        $this->installTool->handleRunOnce();

        $commands = $this->installer->getCommands();

        if (!$drop) {
            unset($commands['DROP']);
            unset($commands['ALTER_DROP']);
        }

        foreach ($commands as $category) {
            foreach ($category as $hash => $sql) {
                $output->writeln('<info>Executing Query: "' . $sql . '"</info>');
                if (!$input->getOption('dry-run')) {
                    $this->installer->execCommand($hash);
                }
            }
        }

        $output->writeln('<info>Success: Database update complete</info>');

    }

}