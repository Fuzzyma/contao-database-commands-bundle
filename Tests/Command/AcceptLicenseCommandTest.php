<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Tests\Command;

use Fuzzyma\Contao\DatabaseCommandsBundle\Command\AcceptLicenseCommand;
use Symfony\Component\Console\Tester\CommandTester;

class AcceptLicenseCommandTest extends CommandTest
{

    public function testInstantiation()
    {
        $command = new AcceptLicenseCommand($this->getFrameworkMock(), $this->getInstallToolMock());
        $this->assertInstanceOf(AcceptLicenseCommand::class, $command);
    }

    public function testAcceptLicenseSuccess()
    {

        $installTool = $this->getInstallToolMock();
        $installTool
            ->expects($this->once())
            ->method('persistConfig')
            ->with('licenseAccepted', true);

        $command = new AcceptLicenseCommand($this->getFrameworkMock(), $installTool);
        $command->setHelperSet($this->getHelperSet("yes"));

        $tester = new CommandTester($command);
        $code = $tester->execute([]);

        $this->assertEquals(0, $code);
        $this->assertContains('Do you want to accept the GNU GENERAL PUBLIC LICENSE', $tester->getDisplay());
        $this->assertContains('Success: License accepted', $tester->getDisplay());
    }

    public function testAcceptLicenseSuccessWithYesOption()
    {

        $installTool = $this->getInstallToolMock();
        $installTool
            ->expects($this->once())
            ->method('persistConfig')
            ->with('licenseAccepted', true);

        $command = new AcceptLicenseCommand($this->getFrameworkMock(), $installTool);
        $command->setHelperSet($this->getHelperSet());

        $tester = new CommandTester($command);
        $code = $tester->execute(['--yes' => true]);

        $this->assertEquals(0, $code);
        $this->assertContains('Success: License accepted', $tester->getDisplay());
    }


    public function testAcceptLicenseFailed()
    {
        $installTool = $this->getInstallToolMock();
        $installTool
            ->expects($this->once())
            ->method('persistConfig')
            ->with('licenseAccepted', false);

        $command = new AcceptLicenseCommand($this->getFrameworkMock(), $installTool);
        $command->setHelperSet($this->getHelperSet("no"));

        $tester = new CommandTester($command);
        $code = $tester->execute([]);

        $this->assertEquals(1, $code);
        $this->assertContains('Do you want to accept the GNU GENERAL PUBLIC LICENSE', $tester->getDisplay());
        $this->assertContains('Error: License was not accepted', $tester->getDisplay());
    }

} 