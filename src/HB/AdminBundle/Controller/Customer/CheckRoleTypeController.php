<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;


use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\CustomerRole;
use Symfony\Component\HttpFoundation\JsonResponse;

class CheckRoleTypeController
{
    /**
     * @param CustomerRole $role
     * @param string       $compareRole
     *
     * @return JsonResponse
     */
    public function handleAction(CustomerRole $role, string $compareRole)
    {
        if ($role->getName() === $compareRole) {
            return Json::ok();
        }

        return Json::error();
    }
}