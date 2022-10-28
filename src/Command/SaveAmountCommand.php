<?php

namespace App\Command;

use App\Entity\Result;
use App\Repository\ResultRepository;
use App\Service\SaveAmountService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:saveAmount',
    description: 'Save current amount result',
)]
class SaveAmountCommand extends Command
{
    private SaveAmountService $saveAmountService;

    public function __construct(SaveAmountService $saveAmountService)
    {
        $this->saveAmountService = $saveAmountService;

        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->setHelp("Cette commande permet d'executer le service qui sauvegarde le benefice ou la perte de valeur au moment de son execution")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output ): int
    {
        $response = $this->saveAmountService->saveAmount();
        if($response === "OK"){
            return Command::SUCCESS;
        }else{
            return Command::FAILURE;
        }

    }

}
