<?php

namespace Fuzzyma\Contao\DatabaseCommandsBundle\Tests\Command;

use Fuzzyma\Contao\DatabaseCommandsBundle\Command\DatabaseAddAdminCommand;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class DatabaseAddAdminCommandTest extends CommandTest
{

    public function testInstantiation()
    {
        $command = new DatabaseAddAdminCommand($this->getFrameworkMock(), $this->getInstallToolMock(), 'en');
        $this->assertInstanceOf(DatabaseAddAdminCommand::class, $command);
    }

    public function testFailsWithoutUserTable()
    {
        $this->expectException(RuntimeException::class);

        $installTool = $this->getInstallToolMock(false);
        $installTool
            ->expects($this->once())
            ->method('hasTable');

        $command = new DatabaseAddAdminCommand($this->getFrameworkMock(), $installTool, 'en');
        $command->setHelperSet($this->getHelperSet());

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertContains('Error: tl_user does not exist', $tester->getDisplay());
        $this->expectException(RuntimeException::class);
    }

    public function testFailsIfAdminUserAlreadyPresentAndUserEntersNo()
    {
        $this->expectException(RuntimeException::class);

        $installTool = $this->getInstallToolMock(true, true);
        $installTool
            ->expects($this->once())
            ->method('hasAdminUser');

        $command = new DatabaseAddAdminCommand($this->getFrameworkMock(), $installTool, 'en');
        $command->setHelperSet($this->getHelperSet("no"));

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertContains('Aborted: Admin entry already present in tl_user table.', $tester->getDisplay());
    }

    /**
     * @dataProvider getInteractiveCommandData
     */
    public function testSuccessIfUserEntersValidData($options, $input, $expected)
    {

        $installTool = $this->getInstallToolMock();
        $installTool
            ->expects($this->once())
            ->method('persistConfig')
            ->with('adminEmail', $expected['email'])
            ->will($this->returnValue(true));


        $command = new DatabaseAddAdminCommand($this->getFrameworkMock(), $installTool, 'en');
        $command->setHelperSet($this->getHelperSet($input));

        $tester = new CommandTester($command);
        $code = $tester->execute($options);

        $expected['force'] = null;

        $this->assertEquals(0, $code);
        $this->assertEquals($expected, $tester->getInput()->getOptions());
        $this->assertContains('Success: Admin user added', $tester->getDisplay());
    }

    public function testSuccessIfAdminAlreadyPresentAndUserUsesForce()
    {

        $options = [
            '--username' => 'myUsername',
            '--name' => 'myName',
            '--email' => 'foo@bar.baz',
            '--password' => 'superSecret',
            '--force' => true
        ];

        $expected = [
            'username' => 'myUsername',
            'name' => 'myName',
            'email' => 'foo@bar.baz',
            'password' => 'superSecret',
            'force' => true
        ];

        $installTool = $this->getInstallToolMock(true, true);

        $command = new DatabaseAddAdminCommand($this->getFrameworkMock(), $installTool, 'en');
        $command->setHelperSet($this->getHelperSet());

        $tester = new CommandTester($command);
        $code = $tester->execute($options);

        $this->assertEquals(0, $code);
        $this->assertEquals($expected, $tester->getInput()->getOptions());
        $this->assertContains('Success: Admin user added', $tester->getDisplay());
    }


    public function getInteractiveCommandData()
    {
        return [
            [
                //no input, ask for everything
                [],
                // all default
                "\n\n\nmyPassword\n",
                // username, name, email, password
                [
                    'username' => 'admin',
                    'name' => 'John Doe',
                    'email' => 'admin@example.com',
                    'password' => 'myPassword'
                ]
            ],
            [
                //username given, ask for everything else
                ['--username' => 'myUsername'],
                "\n\nmyPassword\n",
                [
                    'username' => 'myUsername',
                    'name' => 'John Doe',
                    'email' => 'admin@example.com',
                    'password' => 'myPassword'
                ]
            ],
            [
                ['--username' => 'myUsername', '--name' => 'myName'],
                "\nmyPassword\n",
                [
                    'username' => 'myUsername',
                    'name' => 'myName',
                    'email' => 'admin@example.com',
                    'password' => 'myPassword'
                ]
            ],
            [
                ['--username' => 'myUsername', '--name' => 'myName', '--email' => 'foo@bar.baz'],
                "myPassword\n",
                [
                    'username' => 'myUsername',
                    'name' => 'myName',
                    'email' => 'foo@bar.baz',
                    'password' => 'myPassword'
                ]
            ],
            [
                ['--username' => 'myUsername', '--name' => 'myName', '--email' => 'foo@bar.baz', '--password' => 'superSecret'],
                "",
                [
                    'username' => 'myUsername',
                    'name' => 'myName',
                    'email' => 'foo@bar.baz',
                    'password' => 'superSecret'
                ]
            ]
        ];
    }


} 