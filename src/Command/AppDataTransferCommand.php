<?php

namespace App\Command;

use App\Manager\SiteDataTransferManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppDataTransferCommand extends Command
{
    private $siteDataTransferManager;
    protected static $defaultName = 'app:data-transfer';

    public function __construct(SiteDataTransferManager $siteDataTransferManager)
    {
        $this->siteDataTransferManager = $siteDataTransferManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('phase', InputArgument::OPTIONAL, InputOption::VALUE_NONE, 'Transfer data for the specified phase.')
            ->addOption('reset', 'r', InputArgument::OPTIONAL, 'Reset', false)
            ->addOption('force', 'f', InputArgument::OPTIONAL, 'Reset', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->siteDataTransferManager->setupIO($output, $input);

        $phase = $input->getArgument('phase');
        $reset = $input->getOption('reset');
        $force = $input->getOption('force');

        $this->siteDataTransferManager->process($phase, $reset, $force);

//        //step phase
//
//        $io = new SymfonyStyle($input, $output);
//        $arg1 = $input->getArgument('arg1');
//
//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }
//
//        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
