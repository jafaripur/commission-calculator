<?php

namespace App\Tests\Functional\Command;

use App\Service\BinProvider\Providers\BinFakerProvider;
use App\Service\BinProvider\Providers\BinListProvider;
use App\Service\CurrencyProvider\Providers\CurrencyFakeProvider;
use App\Service\CurrencyProvider\Providers\CurrencyFreakProvider;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CommissionCalculatorCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();

        $container = $this->getContainer();
        $container->set(BinListProvider::class, $container->get(BinFakerProvider::class));
        $container->set(CurrencyFreakProvider::class, $container->get(CurrencyFakeProvider::class));

        $application = new Application($kernel);

        $command = $application->find('commission:calculate');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'file' => __DIR__.'/files/CommissionCalculatorCommandTest.txt',
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('1 => 1', $output);
        $this->assertStringContainsString('0.460138 => 0.47', $output);
        $this->assertStringContainsString('1.5953623550194 => 1.6', $output);
        $this->assertStringContainsString('2.3927176 => 2.4', $output);
        $this->assertStringContainsString('48.272848633947 => 48.28', $output);
    }
}
