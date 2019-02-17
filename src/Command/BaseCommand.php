<?php

// Call this with: php bin/console app:base
Namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class BaseCommand extends Command
{

    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setName( 'app:base' )
                ->setHelp( 'Configure' );
    }

    /**
     * @see Command
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $output->writeln( 'Executed' );
    }

    /**
     * @see Command
     */
    protected function interact( InputInterface $input, OutputInterface $output )
    {
        $output->writeln( 'Interact' );
    }

}
