<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Tests\Command;


use Fuzzyma\Contao\DatabaseCommandsBundle\Command\SetupCommand;
use Symfony\Component\Console\Tester\CommandTester;

class SetupCommandTest extends CommandTest
{

    public function testInstantiation()
    {
        $command = new SetupCommand();
        $this->assertInstanceOf(SetupCommand::class, $command);
    }

    public function testCallsAllCommands()
    {
        $command = new SetupCommand();
        $command->setApplication($this->getApplicationMock());
        $command->setHelperSet($this->getHelperSet());

        $tester = new CommandTester($command);
        $tester->execute([]);
    }

} 