<?php

namespace HB\AdminBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckServicesConnectionCommand extends AbstractConsoleMessage
{
    protected function configure()
    {
        $this->setName("connections:check");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkAMQPConnection($output);
        $this->checkDBconnection($output);
    }

    /**
     * @param OutputInterface $output
     */
    private function checkAMQPConnection(OutputInterface $output)
    {
        $amqpManager = $this->getContainer()->get("amqp.manager");

        $output->writeln("Check AMQP...  ");
        try {
            if ($amqpManager->getConnection()->isConnected()) {
                $this->consoleSuccess("Connected", $output);
            } else {
                $this->consoleError('Connect problem', $output);
            }
        } catch (\Exception $exception) {
            $this->consoleSuccess("Connect problem. ".$exception->getMessage(), $output);
        }
    }

    private function checkDBconnection(OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $output->writeln("Check Database connection...  ");
        try {
            if ($entityManager->getConnection()->connect()) {
                $this->consoleSuccess("Connected", $output);
            } else {
                $this->consoleError('Connect problem', $output);
            }
        } catch (\Exception $exception) {
            $this->consoleSuccess("Connect problem. ".$exception->getMessage(), $output);
        }
    }


}