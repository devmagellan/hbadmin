<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;


use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerTransferWiseAccount;
use HB\AdminBundle\Form\CustomerTransferWiseAccount\CustomerTransferWiseRussiaCardType;
use HB\AdminBundle\Form\CustomerTransferWiseAccount\CustomerTransferWiseRussiaLocalType;
use HB\AdminBundle\Form\CustomerTransferWiseAccount\CustomerTransferWiseUkraineType;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TransferWiseController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * TransferWiseController constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param \Twig_Environment    $twig
     * @param FormHandler          $formHandler
     */
    public function __construct(FormFactoryInterface $formFactory, \Twig_Environment $twig, FormHandler $formHandler)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->formHandler = $formHandler;
    }

    /**
     * @param Customer $customer
     * @param Request  $request
     * @param bool     $forEdit
     *
     * @return Response
     */
    public function handleAction(Customer $customer, Request $request, bool $forEdit = true)
    {
        $type = $request->get('type', null);
        $saved = false;

        if ($customer->getTransferWiseAccount()) {
            $type = $customer->getTransferWiseAccount()->getAccountType();
            $account = $customer->getTransferWiseAccount();
        } else {
            $account = new CustomerTransferWiseAccount($customer, $type);
        }

        switch ($type) {
            case $type === CustomerTransferWiseAccount::TYPE_UA:
                $form = $this->formFactory->create(CustomerTransferWiseUkraineType::class, $account);
                $label = 'Украина';
                break;
            case $type === CustomerTransferWiseAccount::TYPE_RU_LOCAl:
                $form = $this->formFactory->create(CustomerTransferWiseRussiaLocalType::class, $account);
                $label = 'Россия (Российская банковская карта)';
                break;
            case $type === CustomerTransferWiseAccount::TYPE_RU_CARD:
                $form = $this->formFactory->create(CustomerTransferWiseRussiaCardType::class, $account);
                $label = 'Россия (Местный банковский счет)';
                break;
            default:
                throw new \InvalidArgumentException('Unknown transferWise type');
                break;
        }

        if ($this->formHandler->handle($account, $request, $form)) {
            $saved = true;
        }

        $content = $this->twig->render('@HBAdmin/Customer/transferwise.html.twig', [
            'label'    => $label,
            'form'     => $form->createView(),
            'saved'    => $saved,
            'type'     => $type,
            'customer' => $customer,
            'forEdit'  => $forEdit,
        ]);

        return new Response($content);
    }
}