<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Command;

use Contao\CoreBundle\Command\AbstractLockedCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AcceptLicenseCommand extends AbstractLockedCommand
{

    private $license = 'no';

    protected function configure()
    {

        $this
            ->setName('contao:license')
            ->setDescription('Accept the contao license');
    }

    public function getQuestion($question, $default, $sep = ':')
    {
        return $default ? sprintf('<info>%s</info> [<comment>%s</comment>]%s ', $question, $default, $sep) : sprintf('<info>%s</info>%s ', $question, $sep);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {

        $questionHelper = $this->getHelper('question');

        $question = new Question($this->getQuestion('Do you want to accept the GNU GENERAL PUBLIC LICENSE', 'yes'), 'yes');
        $this->license = $questionHelper->ask($input, $output, $question);

    }

    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {

        $this->getContainer()->get('contao.framework')->initialize();

        if($this->license == 'no'){
            $this->getContainer()->get('contao.install_tool')->persistConfig('licenseAccepted', false);
            $output->writeln('<error>Error: License was not accepted</error>');
            return;
        }


        $this->getContainer()->get('contao.install_tool')->persistConfig('licenseAccepted', true);

        $output->writeln('<info>Success: License accepted</info>');

    }

}