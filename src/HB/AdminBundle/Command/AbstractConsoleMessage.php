<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractConsoleMessage extends ContainerAwareCommand
{

    protected function error(string $message, OutputInterface $output)
    {
        $output->writeln('<error>'.$message.'</error>');
    }

    protected function consoleError(string $message, OutputInterface $output)
    {
        $output->writeln('<error>'.$message.'</error>');
    }

    protected function consoleSuccess(string $message, OutputInterface $output)
    {
        $output->writeln('<info>'.$message.'</info>');
    }
}