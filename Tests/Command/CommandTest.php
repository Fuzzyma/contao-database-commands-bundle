<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Tests\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\InstallationBundle\Database\Installer;
use Contao\InstallationBundle\InstallTool;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

class CommandTest extends \PHPUnit_Framework_TestCase
{


    protected $mockSql = [
        'DROP' => ['hash1' => 'DROP TABLE table_name'],
        'ALTER_DROP' => ['hash2' => 'ALTER TABLE table_name DROP COLUMN column_name'],
        'CREATE' => [
            'hash3' => 'CREATE TABLE Persons
                (
                    PersonID int,
                    LastName varchar(255),
                    FirstName varchar(255),
                    Address varchar(255),
                    City varchar(255)
                )',

        ],
        'ALTER_ADD' => ['hash4' => 'ALTER TABLE table_name ADD column_name varchar(255)'],
    ];

    protected function getHelperSet($input = "")
    {
        $question = new QuestionHelper();
        $question->setInputStream($this->getInputStream($input));

        return new HelperSet(array(new FormatterHelper(), $question));
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fwrite($stream, $input . str_repeat("\n", 10));
        rewind($stream);

        return $stream;
    }

    protected function getFrameworkMock()
    {
        return $this->getMockBuilder(ContaoFramework::class)
            ->disableOriginalConstructor()
            ->setMethods(['initialize'])
            ->getMock();
    }

    protected function getInstallToolMock($hasTable = true, $hasAdminUser = false)
    {
        $installTool = $this->getMockBuilder(InstallTool::class)
            ->disableOriginalConstructor()
            ->setMethods(['persistConfig', 'persistAdminUser', 'hasAdminUser', 'hasTable', 'handleRunOnce'])
            ->getMock();

        $installTool->method('hasTable')->with('tl_user')->will($this->returnValue($hasTable));
        $installTool->method('hasAdminUser')->will($this->returnValue($hasAdminUser));

        return $installTool;
    }

    protected function getInstallerMock()
    {

        $installer = $this->getMockBuilder(Installer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCommands', 'execCommand'])
            ->getMock();

        $installer->method('getCommands')->will($this->returnValue($this->mockSql));
        return $installer;
    }

    protected function getApplicationMock()
    {
        $application = $this->getMockBuilder(Application::class)
            ->setMethods(['find'])
            ->getMock();

        $application->expects($this->at(0))->method('find')->with('contao:license')->will($this->returnValue(
            $this->getMockBuilder(Command::class)->disableOriginalConstructor()->setMethods(['run'])->getMock()
        ));
        $application->expects($this->at(1))->method('find')->with('doctrine:database:create')->will($this->returnValue(
            $this->getMockBuilder(Command::class)->disableOriginalConstructor()->setMethods(['run'])->getMock()
        ));
        $application->expects($this->at(2))->method('find')->with('contao:database:update')->will($this->returnValue(
            $this->getMockBuilder(Command::class)->disableOriginalConstructor()->setMethods(['run'])->getMock()
        ));
        $application->expects($this->at(3))->method('find')->with('contao:database:addAdmin')->will($this->returnValue(
            $this->getMockBuilder(Command::class)->disableOriginalConstructor()->setMethods(['run'])->getMock()
        ));

        return $application;
    }
}