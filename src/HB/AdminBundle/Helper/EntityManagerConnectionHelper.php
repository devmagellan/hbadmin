<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;

class EntityManagerConnectionHelper
{
    /**
     * Attempt Fight against SQLSTATE[HY000]: General error: MySQL server has gone away
     *
     * @param EntityManagerInterface $entityManager
     *
     * @throws \Exception
     */
    public static function lazyConnect(EntityManagerInterface $entityManager)
    {
        if (!$entityManager->getConnection()->isConnected()) {
            $entityManager->getConnection()->connect();
            if ($entityManager->getConnection()->isConnected() === false) {
                throw new \Exception('DB connection error!');
            }
        }
    }
}