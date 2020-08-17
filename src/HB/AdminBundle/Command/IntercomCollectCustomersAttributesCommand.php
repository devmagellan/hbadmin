<?php

namespace HB\AdminBundle\Command;

use HB\AdminBundle\Entity\Customer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Stopwatch\Stopwatch;

class IntercomCollectCustomersAttributesCommand extends ContainerAwareCommand
{
    private const LAST_ACTIVITY_FOR_UPDATE = 3600;

    protected function configure()
    {
        $this->setName("intercom:customers:update")
            ->addArgument("email", InputArgument::OPTIONAL, "The user email.");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Intercom customer attributes collector');

        $manager = $this->getContainer()->get("doctrine.orm.entity_manager");
        $intercom_attributes_collector = $this->getContainer()->get("intercom.attributes_collector");
        $sentryErrorHandler = $this->getContainer()->get('sentry.client');

        $customers = [];
        $email = $input->getArgument("email");

        if ($email) {
            /** @var Customer $customer */
            $customer = $manager->getRepository(Customer::class)->findOneBy(['email' => $email]);
            if ($customer) {
                $customers[] = $customer;
            } else {
                $output->writeln('Customer with %s not found.', $email);
            }
        } else {
            $customers = $manager->getRepository(Customer::class)->findAll();
        }

        $timer = new Stopwatch();
        $timer->start('update');
        $currentDate = new \DateTime();

        foreach ($customers as $customer) {
            if (!$customer->isAdmin() && $customer->getLastActivityAt()) {

                $activityAt = $customer->getLastActivityAt();
                $diff = $currentDate->getTimestamp() - $activityAt->getTimestamp();

                if ($diff <= self::LAST_ACTIVITY_FOR_UPDATE) {
                    try {
                        $intercom_attributes_collector->updateCustomerAttributes($customer);
                        $output->writeln(sprintf('Update customer id: %s, email: %s', $customer->getId(), $customer->getEmail()));
                    } catch (\Exception $exception) {
                        $sentryErrorHandler->captureException($exception);
                    }
                }
            }
        }
        $seconds = $timer->stop('update')->getDuration() / 1000;
        $output->writeln('Update time: '.$seconds.' s');

    }
}