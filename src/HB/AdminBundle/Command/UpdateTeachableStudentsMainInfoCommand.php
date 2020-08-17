<?php

namespace HB\AdminBundle\Command;

use HB\AdminBundle\Entity\Teachable\Webhook\EnrollmentCreated;
use HB\AdminBundle\Entity\Teachable\Webhook\LectureProgressCreated;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class UpdateTeachableStudentsMainInfoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("teachable:student:main_info:refresh");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mapper = $this->getContainer()->get('teachable.webhooks_mapper');

        $output->writeln('update EnrollmentCreated webhooks...');
        foreach ($this->getEnrollmentWebhooks() as $enrollmentCreated) {
            $mapper->updateEnrollment($enrollmentCreated);
            $output->writeln('Updated id: '. $enrollmentCreated->getId());
        }

        $output->writeln('update LectureProgressCreated webhooks...');
        /** @var LectureProgressCreated $lectureProgressCreated */
        foreach ($this->getLectureProgressWebhooks() as $lectureProgressCreated) {
            $output->writeln('Begin id: ' .$lectureProgressCreated->getId());
            $mapper->updateLectureProgress($lectureProgressCreated);
            $output->writeln('Updated.');
        }
    }

    /**
     * @return EnrollmentCreated[]
     */
    private function getEnrollmentWebhooks()
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        return $em->getRepository(EnrollmentCreated::class)->findBy([], ['id' => 'ASC']);
    }

    /**
     * @return LectureProgressCreated[]
     */
    private function getLectureProgressWebhooks()
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        return $em->getRepository(LectureProgressCreated::class)->findBy([], ['id' => 'ASC']);
    }
}