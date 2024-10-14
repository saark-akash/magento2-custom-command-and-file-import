<?php

declare(strict_types=1);

namespace Akash\MageTest\Console\Command;

use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class SomeCommand extends Command
{
    public const SAMPLE_CSV = 'sample-csv';
    public const SAMPLE_JSON = 'sample-json';
    private const SOURCE = 'source';
    private const PROFILE_NAME = 'profile-name';
    /**
     * @var \Akash\MageTest\Api\ReadFileRepositoryInterface
     */
    public $readFileRepository;
    public function __construct(
        \Akash\MageTest\Api\ReadFileRepositoryInterface $readFileRepository
    ) {
        $this->readFileRepository = $readFileRepository;
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->setName('customer:import');
        $this->setDescription('Customer import command.');
        $this->setDefinition([
            new InputArgument(
                self::SOURCE,
                InputArgument::REQUIRED,
                'File source'
            ),
            new InputArgument(
                self::PROFILE_NAME,
                InputArgument::REQUIRED,
                'File name'
            )
        ]);

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;
        try {
            $source = $input->getArgument(self::SOURCE);
            $profileName = $input->getArgument(self::PROFILE_NAME);
            $readReturn = $this->readFileRepository->readFile($source, $profileName);
            if ($readReturn == 0) {
                $output->writeln('<info>Either file not exist or not readable.</info>');
            } elseif ($readReturn == 1) {
                $output->writeln('<info>Customer created.</info>');
                $output->writeln('<comment>END.</comment>');
            } else {
                if ($readReturn != "") {
                    $output->writeln('<comment>' . $readReturn . ' are the list of customers already exists!.</comment>');
                    $output->writeln('<info>Rest all customers are created.</info>');
                    $output->writeln('<comment>END.</comment>');
                } else
                    $output->writeln('<comment>Something went wrong.</comment>');
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }
            // we must have an exit code higher than zero to indicate something was wrong
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
        return $exitCode;
    }
}
