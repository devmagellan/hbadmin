<?php

namespace HB\AdminBundle\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use HB\AdminBundle\Entity\CoursePriceBlock;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPacket;
use HB\AdminBundle\Entity\CustomerRole;
use HB\AdminBundle\Entity\Types\CustomerPacketType;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

class ProjectInitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("user:create")
            ->addArgument("email", InputArgument::OPTIONAL, "The email/username for admin user.")
            ->addArgument("password", InputArgument::OPTIONAL, "The password for admin user.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Roles / Packets / Course price block add and optional admin create (role ADMIN)',
            '========================',
            '',
        ]);

        $username = $input->getArgument("email");
        $password = $input->getArgument("password");

        $manager = $this->getContainer()->get("doctrine.orm.entity_manager");

        $adminRole = $this->addRole(CustomerRole::ROLE_ADMIN, $output);

        $this->addDefaultPackets($output);

        if ($username && $password) {
            $customer = new Customer();

            $customer->setRole($adminRole);
            $customer->setEmail($username);

            $pasHash = $this->getContainer()->get("security.password_encoder")->encodePassword($customer, $password);
            $customer->setPassword($pasHash);
            try {
                $this->addPacketTypeToAdmin($customer);
                $manager->persist($customer);
                $manager->flush();


                $output->writeln("Customer ".$username." was added.");
            } catch (UniqueConstraintViolationException $exception) {
                $output->writeln("Customer with such email already exist");
            }
        }

        $this->addRole(CustomerRole::ROLE_AUTHOR, $output);
        $this->addRole(CustomerRole::ROLE_PRODUCER, $output);
        $this->addRole(CustomerRole::ROLE_MANAGER, $output);



        $this->addDefaultCoursePriceBlocks($output);
    }

    /**
     * @param string          $role
     * @param OutputInterface $output
     *
     * @return CustomerRole
     */
    private function addRole(string $role, OutputInterface $output): CustomerRole
    {
        $manager = $this->getContainer()->get("doctrine.orm.entity_manager");

        $dbRole = $manager->getRepository(CustomerRole::class)->findOneBy(["name" => $role]);

        if (!$dbRole) {
            $dbRole = new CustomerRole($role);
            $dbRole->setName($role);
            $manager->persist($dbRole);
            $manager->flush();

            $output->writeln("Role ".$role." was added.");
        }

        return $dbRole;
    }

    private function addDefaultPackets(OutputInterface $output): void
    {
        $defaultPackets = CustomerPacketType::possibleTypes();
        $manager = $this->getContainer()->get("doctrine.orm.entity_manager");
        $packetRepository = $manager->getRepository(CustomerPacket::class);

        foreach ($defaultPackets as $packet) {
            $isExist = $packetRepository->findOneBy(['type' => $packet]);

            if (!$isExist) {
                $newPacket = new CustomerPacket(new CustomerPacketType($packet));
                $manager->persist($newPacket);
                $manager->flush();

                $output->writeln("Packet ".$packet." was added.");
            }
        }
    }

    /**
     * @param Customer $customer
     */
    private function addPacketTypeToAdmin(Customer $customer)
    {
        $manager = $this->getContainer()->get("doctrine.orm.entity_manager");
        $packet = $manager->getRepository(CustomerPacket::class)->findOneBy(['type' => CustomerPacketType::PREMIUM]);
        $customer->setPacket($packet);
        $manager->persist($packet);
    }

    /**
     * @param OutputInterface $output
     */
    private function addDefaultCoursePriceBlocks(OutputInterface $output): void
    {
        $defaultCoursePriceBlocks = [
            [
                'priority' => 0,
                'name'     => 'Базовый',
                'type'     => CoursePriceBlock::TYPE_BASIC,
            ],
            [
                'priority' => 1,
                'name'     => 'Оптимальный',
                'type'     => CoursePriceBlock::TYPE_OPTIMAL,

            ],
            [
                'priority' => 2,
                'name'     => 'VIP',
                'type'     => CoursePriceBlock::TYPE_VIP,
            ],
        ];
        $manager = $this->getContainer()->get("doctrine.orm.entity_manager");
        $blockRepository = $manager->getRepository(CoursePriceBlock::class);

        foreach ($defaultCoursePriceBlocks as $block) {
            $isExist = $blockRepository->findOneBy(['title' => $block['name']]);

            if (!$isExist) {
                $newPriceBlock = new CoursePriceBlock($block['name'], $block['priority'], $block['type']);
                $manager->persist($newPriceBlock);
                $manager->flush();

                $output->writeln("Price block ".$block['name']." was added.");
            }
        }
    }

}