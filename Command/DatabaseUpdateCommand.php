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
            ->setDefinition([
                new InputOption(
                    'dump-sql', null, InputOption::VALUE_NONE,
                    'Dumps the generated SQL statements to the screen (does not execute them).'
                ),
                new InputOption(
                    'force', 'f', InputOption::VALUE_NONE,
                    'Causes the generated SQL statements to be physically executed against your database.'
                ),
                new InputOption(
                    'drop', 'd', InputOption::VALUE_NONE,
                    'Includes table and column drops'
                ),
            ]);
    }

    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $dumpSql = !!$input->getOption('dump-sql');
        $force   = !!$input->getOption('force');
        $drop = !!$input->getOption('drop');

        $this->getContainer()->get('contao.framework')->initialize();
        $this->getContainer()->get('contao.install_tool')->handleRunOnce();

        $installer = $this->getContainer()->get('contao.installer');

        $commands = $installer->getCommands();

        if (!$drop) {
            unset($commands['DROP']);
            unset($commands['ALTER_DROP']);
        }

        $sqls = [];

        foreach ($commands as $category) {
            foreach ($category as $hash => $sql) {
                $sqls[$hash] = $sql;
            }
        }

        if (0 === count($sqls)) {
            $output->writeln('Nothing to update - your database is already in sync with the dca files.');
            return 0;
        }

        if ($dumpSql) {
            $output->writeln(implode(';' . PHP_EOL, $sqls) . ';');
        }

        if ($force) {
            if ($dumpSql) {
                $output->writeln('');
            }

            $output->writeln('Updating database schema...');

            foreach ($sqls as $hash => $sql) {
                $installer->execCommand($hash);
            }

            $pluralization = (1 === count($sqls)) ? 'query was' : 'queries were';

            $output->writeln(sprintf(
                'Database schema updated successfully! "<info>%s</info>" %s executed',
                count($sqls),
                $pluralization
            ));
        }

        if ($dumpSql || $force) {
            return 0;
        }

        $output->writeln(sprintf('The Database-Update-Tool would execute <info>"%s"</info> queries to update the database.', count($sqls)));
        $output->writeln('Please run the operation by passing one - or more - of the following options:');
        $output->writeln(sprintf('    <info>%s --force</info> to execute the command', $this->getName()));
        $output->writeln(sprintf('    <info>%s --dump-sql</info> to dump the SQL statements to the screen', $this->getName()));
        $output->writeln(sprintf('    <info>%s --drop</info> to include table and column drops', $this->getName()));

        return 1;
    }
}
