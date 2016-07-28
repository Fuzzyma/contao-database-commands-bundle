<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Tests\Command;


use Fuzzyma\Contao\DatabaseCommandsBundle\Command\DatabaseUpdateCommand;
use Symfony\Component\Console\Tester\CommandTester;

class DatabaseUpdateCommandTest extends CommandTest
{

    public function testInstantiation()
    {
        $command = new DatabaseUpdateCommand($this->getFrameworkMock(), $this->getInstallToolMock(), $this->getInstallerMock());
        $this->assertInstanceOf(DatabaseUpdateCommand::class, $command);
    }

    public function testSkipDropsByDefault()
    {

        $installer = $this->getInstallerMock();
        $installer
            ->expects($this->exactly(2))
            ->method('execCommand');

        $command = new DatabaseUpdateCommand($this->getFrameworkMock(), $this->getInstallToolMock(), $installer);
        $command->setHelperSet($this->getHelperSet());

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertContains($this->mockSql['CREATE']['hash3'], $tester->getDisplay());
        $this->assertContains($this->mockSql['ALTER_ADD']['hash4'], $tester->getDisplay());
    }

    public function testExecuteAllIncludingDrops()
    {

        $installer = $this->getInstallerMock();
        $installer
            ->expects($this->exactly(4))
            ->method('execCommand');

        $command = new DatabaseUpdateCommand($this->getFrameworkMock(), $this->getInstallToolMock(), $installer);
        $command->setHelperSet($this->getHelperSet());

        $tester = new CommandTester($command);
        $tester->execute(['--drop' => true]);

        $this->assertContains($this->mockSql['DROP']['hash1'], $tester->getDisplay());
        $this->assertContains($this->mockSql['ALTER_DROP']['hash2'], $tester->getDisplay());
        $this->assertContains($this->mockSql['CREATE']['hash3'], $tester->getDisplay());
        $this->assertContains($this->mockSql['ALTER_ADD']['hash4'], $tester->getDisplay());
    }

    public function testDryRun()
    {

        $installer = $this->getInstallerMock();
        $installer
            ->expects($this->never())
            ->method('execCommand');

        $command = new DatabaseUpdateCommand($this->getFrameworkMock(), $this->getInstallToolMock(), $installer);
        $command->setHelperSet($this->getHelperSet());

        $tester = new CommandTester($command);
        $tester->execute(['--drop' => true, '--dry-run' => true]);

        $this->assertContains($this->mockSql['DROP']['hash1'], $tester->getDisplay());
        $this->assertContains($this->mockSql['ALTER_DROP']['hash2'], $tester->getDisplay());
        $this->assertContains($this->mockSql['CREATE']['hash3'], $tester->getDisplay());
        $this->assertContains($this->mockSql['ALTER_ADD']['hash4'], $tester->getDisplay());
    }

} 