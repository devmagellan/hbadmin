<?php

namespace HB\AdminBundle\Command;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarDate;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * This command is temporary for switch 1 webinar date to many
 */
class ReplaceWebinarDateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("webinar:replace:date");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get("doctrine.orm.entity_manager");

        $webinars = $manager->getRepository(SalesFunnelWebinar::class)->findAll();

        /** @var SalesFunnelWebinar $webinar */
        foreach ($webinars as $webinar) {
            if ($webinar->getDates()->count() === 0) {
                try {
                    $webinarDate = new WebinarDate();
                    $webinarDate->setTime($webinar->getTime());
                    $webinarDate->setTimezone($webinar->getTimezone());
                    $webinarDate->setWebinar($webinar);
                    $webinar->addDate($webinarDate);

                    $manager->persist($webinarDate);
                    $manager->persist($webinar);
                    $manager->flush();
                } catch (\Exception $exception) {
                    $output->writeln($exception->getMessage());
                }
            }
        }
    }

}