<?php

namespace App\Command;

use App\Service\NotifyService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyCommand extends Command
{
    private $notifyService;

	public function __construct(NotifyService $notifyService)
    {
        $this->notifyService = $notifyService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:notify')

            // the short description shown while running "php bin/console list"
            ->setDescription('Check prices and notify users.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command runs through the notifications table and notifies users whose criteria is met.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->notifyService->run();
    }


}