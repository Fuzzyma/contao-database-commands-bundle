<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AcceptLicenseCommand extends BaseCommand
{


    protected function configure()
    {

        $this
            ->setName('contao:license')
            ->setDescription('Accept the contao license')
            ->addOption('yes', 'y', InputOption::VALUE_NONE, 'Accept the GNU GENERAL PUBLIC LICENSE');
    }

    public function getQuestion($question, $default, $sep = ':')
    {
        return $default ? sprintf('<info>%s</info> [<comment>%s</comment>]%s ', $question, $default, $sep) : sprintf('<info>%s</info>%s ', $question, $sep);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {

        if ($input->getOption('yes')) return;

        $questionHelper = $this->getHelper('question');
        $question = new Question($this->getQuestion('Do you want to accept the GNU GENERAL PUBLIC LICENSE', 'yes'), 'yes');
        $input->setOption('yes', $questionHelper->ask($input, $output, $question) == 'yes' ? true : false);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->framework->initialize();

        if (!$input->getOption('yes')) {
            $this->installTool->persistConfig('licenseAccepted', false);
            $output->writeln('<error>Error: License was not accepted</error>');
            return 1;
        }


        $this->installTool->persistConfig('licenseAccepted', true);

        $output->writeln('<info>Success: License accepted</info>');

        return 0;

    }

}