<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ParseWebPageCommandTest extends KernelTestCase
{
    public function testFetchTagRightContent()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('app:parse-webpage');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'url' => 'http://example.org',
            'tags' => ['h1', 'p']
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Example Domain', $output);
        $this->assertStringContainsString('This domain is for use in illustrative examples in documents. You may use this domain in literature without prior coordination or asking for permission.', $output);
    }

    public function testRequestWrongDomainName()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('app:parse-webpage');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'url' => 'http://example111.org',
            'tags' => ['h1', 'p']
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString("Couldn't resolve host name", $output);
    }
}
