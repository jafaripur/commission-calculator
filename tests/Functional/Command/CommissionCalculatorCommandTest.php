<?php

namespace App\Tests\Functional\Command;

use App\Service\BinProvider\Providers\BinFakerProvider;
use App\Service\BinProvider\Providers\BinListProvider;
use App\Service\CurrencyProvider\Providers\CurrencyFakeProvider;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CommissionCalculatorCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $container = $this->getContainer();

        $container->set(CurrencyFreakProvider::class, $container->get(CurrencyFakeProvider::class));
        $container->set(BinListProvider::class, $container->get(BinFakerProvider::class));

        $command = $application->find('commission:calculate');

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'file' => __DIR__.'/files/CommissionCalculatorCommandTest.txt',
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('1 => 1', $output);
        $this->assertStringContainsString('0.4587405 => 0.46', $output);
        $this->assertStringContainsString('1.5852529545926 => 1.59', $output);
        $this->assertStringContainsString('2.3854506 => 2.39', $output);
        $this->assertStringContainsString('48.081848267838 => 48.09', $output);
    }
}
