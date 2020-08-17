<?php

namespace HB\AdminBundle\Service;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 *  Form handler for simple validation and save
 */
class FormHandler
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @var ValidatorInterface $validator
     */
    private $validator;

    /**
     * @var FlashBagInterface $flashBag
     */
    private $flashBag;

    /**
     * FormHandler constructor.
     *
     * @param EntityManager      $entityManager
     * @param ValidatorInterface $validator
     * @param FlashBagInterface  $flashBag
     */
    public function __construct(EntityManager $entityManager, ValidatorInterface $validator, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->flashBag = $flashBag;
    }

    /**
     * @param object        $entity
     * @param Request       $request
     * @param FormInterface $form
     *
     * @return bool
     */
    public function handle($entity, Request $request, FormInterface $form)
    {
        try {
            $res = $this->process($entity, $request, $form);
            return $res;
        } catch (\Exception $exception) {
            $this->flashBag->add('error', 'Ошибка валидации формы. '.$exception->getMessage());
        }

        return false;
    }

    /**
     * @param object        $entity
     * @param Request       $request
     * @param FormInterface $form
     *
     * @return bool
     */
    private function process(object $entity, Request $request, FormInterface $form): bool
    {
        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $errors = $this->validator->validate($form);

                if (count($errors) > 0) {
                    return false;
                } else {
                    $this->entityManager->persist($entity);
                    $this->entityManager->flush();
                    return true;
                }
            }
        }

        return false;
    }
}
