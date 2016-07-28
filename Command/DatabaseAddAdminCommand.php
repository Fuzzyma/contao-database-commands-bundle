<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\InstallationBundle\InstallTool;
use Patchwork\Utf8;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class DatabaseAddAdminCommand extends BaseCommand
{

    private $locale;

    public function __construct(ContaoFramework $framework, InstallTool $installTool, $locale)
    {
        parent::__construct($framework, $installTool);

        $this->locale = $locale;
    }


    protected function configure()
    {

        $this
            ->setName('contao:database:addAdmin')
            ->setDescription('Adds an admin entry into the database')
            ->setDefinition(array(
                new InputOption('username', 'u', InputOption::VALUE_REQUIRED, 'Username'),
                new InputOption('name', 'a', InputOption::VALUE_REQUIRED, 'Name'),
                new InputOption('email', 'm', InputOption::VALUE_REQUIRED, 'Email'),
                new InputOption('password', 'p', InputOption::VALUE_REQUIRED, 'Password'),
                new InputOption('force', 'f', InputOption::VALUE_NONE | InputOption::VALUE_OPTIONAL, 'Add admin even if there is already an admin in the table')
            ));
    }

    public function getQuestion($question, $default, $sep = ':')
    {
        return $default ? sprintf('<info>%s</info> [<comment>%s</comment>]%s ', $question, $default, $sep) : sprintf('<info>%s</info>%s ', $question, $sep);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {

        if (!$this->installTool->hasTable('tl_user')) {
            throw new RuntimeException('<error>Error: tl_user does not exist</error>', 1);
        }


        $questionHelper = $this->getHelper('question');

        if ($this->installTool->hasAdminUser() && !$input->getOption('force')) {
            $question = new Question($this->getQuestion('Admin entry already present in tl_user table. Add anyway?', 'no'), 'no');
            if ($questionHelper->ask($input, $output, $question) !== 'yes') {
                throw new RuntimeException('<error>Aborted: Admin entry already present in tl_user table.</error>', 2);
            }
        }

        $username = $input->getOption('username');
        $question = new Question($this->getQuestion('Enter a username', 'admin'), 'admin');
        $question->setValidator(function ($a) {
            return $this->usernameValidator($a);
        });
        $question->setMaxAttempts(3);

        if (!$username) {
            $username = $questionHelper->ask($input, $output, $question);
        } else {
            try {
                $this->usernameValidator($username);
            } catch (\RuntimeException $e) {
                $output->writeln('<error>The username must not contain whitespaces or any of #()\/<=></error>');
                $username = $questionHelper->ask($input, $output, $question);
            }
        }

        $name = $input->getOption('name');
        if (!$name) {
            $question = new Question($this->getQuestion('Enter a name', 'John Doe'), 'John Doe');
            $name = $questionHelper->ask($input, $output, $question);
        }


        $email = $input->getOption('email');
        $question = new Question($this->getQuestion('Enter an email', 'admin@example.com'), 'admin@example.com');
        $question->setValidator(function ($a) {
            return $this->emailValidator($a);
        });
        $question->setMaxAttempts(3);

        if (!$email) {
            $email = $questionHelper->ask($input, $output, $question);
        } else {
            try {
                $this->emailValidator($email);
            } catch (\RuntimeException $e) {
                $output->writeln('<error>The given email is not valid</error>');
                $email = $questionHelper->ask($input, $output, $question);
            }
        }

        $password = $input->getOption('password');
        $minLength = $this->installTool->getConfig('minPasswordLength');

        $question = new Question('<info>Enter a password:</info> ');
        $question->setValidator(function ($answer) use ($minLength, $username) {
            return $this->passwordValidator($answer, $minLength, $username);
        });
        // $question->setHidden(true); see symfony/symfony#19463
        $question->setMaxAttempts(3);

        if (!$password) {
            $password = $questionHelper->ask($input, $output, $question);
        } else {
            try {
                $this->passwordValidator($password, $minLength, $username);
            } catch (\RuntimeException $e) {
                $output->writeln('<error>The given password is too short (8 characters minimum) or not valid</error>');
                $password = $questionHelper->ask($input, $output, $question);
            }
        }

        $input->setOption('username', $username);
        $input->setOption('name', $name);
        $input->setOption('email', $email);
        $input->setOption('password', $password);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->framework->initialize();

        $username = $input->getOption('username');
        $name = $input->getOption('name');
        $email = $input->getOption('email');
        $password = $input->getOption('password');

        $minLength = $this->installTool->getConfig('minPasswordLength');

        $this->usernameValidator($username);
        $this->emailValidator($email);
        $this->passwordValidator($password, $minLength, $username);

        $this->installTool->persistConfig('adminEmail', $email);

        $this->installTool->persistAdminUser(
            $username,
            $name,
            $email,
            $password,
            $this->locale
        );

        $output->writeln('<info>Success: Admin user added</info>');

    }


    private function usernameValidator($username)
    {
        if (preg_match('/[#()\/<=>]/', $username)) {
            throw new \RuntimeException(
                'The username must not contain any of #()\/<=>'
            );
        }

        if (false !== strpos($username, ' ')) {
            throw new \RuntimeException(
                'The username must not contain any whitespaces'
            );
        }

        return $username;
    }

    private function emailValidator($email)
    {
        if ($email !== filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException(
                'The given email is not valid'
            );
        }

        return $email;
    }

    private function passwordValidator($password, $minLength, $username)
    {
        if (Utf8::strlen($password) < $minLength) {
            throw new \RuntimeException(
                'The given password is too short (Minimum ' . $minLength . ' characters)'
            );
        }

        if ($password == $username) {
            throw new \RuntimeException(
                'Username and password must not be the same'
            );
        }

        return $password;
    }

}