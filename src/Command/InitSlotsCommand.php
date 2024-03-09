<?php

namespace App\Command;

use App\Exception\InvalidSlotNumberArgumentException;
use App\Repository\SlotRepository;
use App\Service\SlotService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:modify-slots',
    description: "Modify the number of slots.\nWarning: Data may be lost if the number is changed to a lower value.",
)]
class InitSlotsCommand extends Command
{
    const JUST_COUNT_OPTION = 'just-count';

    public function __construct(private SlotService $slotService, private SlotRepository $slotRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                name: 'number',
                mode: InputArgument::REQUIRED,
                description: 'Number of slots in le cd-loader.'
            )->addOption(
                name: self::JUST_COUNT_OPTION,
                shortcut: 'c',
                mode: InputOption::VALUE_NONE,
                description: 'Returns the actual number of slots without modification.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $number = (int)$input->getArgument('number');
        try {
            if ($input->getOption(self::JUST_COUNT_OPTION)) {
                $io->info('📀 The actual number of slots : '.count($this->slotRepository->getAll()));

                return Command::SUCCESS;
            }
            if ($number < 1 || $number > 1000) {
                throw new InvalidSlotNumberArgumentException('The number of slots must be between 1 and 1000.');
            }
            $this->slotService->updateSlotsNumber($number);
        } catch (InvalidSlotNumberArgumentException $throwable) {
            $io->error('⚠ An error occurred :'.$throwable->getMessage());

            return Command::INVALID;
        } catch (\Throwable $throwable) {
            $io->error('⚠ An error occurred :'.$throwable->getMessage());

            return Command::FAILURE;
        }
        $io->success('📀 The number of slots have been updated to '.$number);

        return Command::SUCCESS;
    }
}
