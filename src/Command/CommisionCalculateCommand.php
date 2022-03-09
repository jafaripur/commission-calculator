<?php

declare(strict_types=1);

namespace App\Command;

use App\Commission\Commissions\EuroCommission;
use App\Service\BinProvider\Interface\BinProviderInterface;
use App\Service\CurrencyProvider\Interface\CurrencyProviderInterface;
use Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CommisionCalculateCommand extends Command
{
    protected static $defaultName = 'commission:calculate';
    protected static $defaultDescription = 'Calculate diffrent commission for EU and none-EU country';

    public function __construct(
        private CurrencyProviderInterface $currencyFreak,
        private BinProviderInterface $binList,
        private BinProviderInterface $binTable,
        private BinProviderInterface $binCodes,
        private ContainerBagInterface $params
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('File path as argument');

        $this->addArgument('file', InputArgument::REQUIRED, 'File path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $targetCurrency = $this->params->get('commission_target_currency');
        $euRate = $this->params->get('commission_eu_rate');
        $noneEuRate = $this->params->get('commission_none_eu_rate');

        foreach ($this->loadFile($input->getArgument('file')) as $line) {
            $json = json_decode($line, true, 2, JSON_THROW_ON_ERROR);
            $euCommission = new EuroCommission(
                (string) $json['bin'],
                (float) $json['amount'],
                (string) $json['currency'],
                $this->binList, // $this->binCodes, // $this->binTable
                $this->currencyFreak,
                $euRate,
                $noneEuRate,
            );

            $output->writeln(
                sprintf(
                    '%s => %s',
                    $euCommission->calculate($targetCurrency, false),
                    $euCommission->calculate($targetCurrency)
                )
            );
        }

        return Command::SUCCESS;
    }

    /**
     * Read and load file.
     *
     * @return Generator<string>
     */
    private function loadFile(string $file): Generator
    {
        if (!file_exists($file) || !is_file($file)) {
            throw new \InvalidArgumentException('File not exist');
        }

        if (!$fileResouce = fopen($file, 'r')) {
            throw new \InvalidArgumentException(sprintf('Can not read file: %s', $file));
        }

        $linesCount = 0;

        while (($line = fgets($fileResouce)) !== false) {
            yield $line;
            ++$linesCount;
        }

        fclose($fileResouce);

        return $linesCount;
    }
}
