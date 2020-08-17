<?php

namespace HB\AdminBundle\Controller;

use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPaymentAccount;
use HB\AdminBundle\Validator\PaymentAccountValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $authUtils = $this->get('security.authentication_utils');
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername() ? $authUtils->getLastUsername() : $request->getSession()->get('last_username');

        return $this->render("@HBAdmin/Login/index.html.twig", [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function routerAction(): RedirectResponse
    {
        /** @var Customer $user */
        $user = $this->getUser();
        $redirect = $this->generateUrl('login');

        if ($user) {
            if ($user->isProducer() || $user->isAuthor()) {

                if (PaymentAccountValidator::isValid($this->getCustomerPaymentAccount($user), $user)) {
                    $redirect = $this->generateUrl('hb.courses.list');
                } else {
                    $redirect = $this->generateUrl('hb.customer.self.edit');
                }

            } else {
                $redirect = $this->generateUrl('hb.courses.list');
            }
        }

        return new RedirectResponse($redirect);
    }

    /**
     * Fallback if customer has no payment account
     *
     * @param Customer $customer
     *
     * @return CustomerPaymentAccount
     */
    private function getCustomerPaymentAccount(Customer $customer):CustomerPaymentAccount
    {
        if ($customer->getPaymentAccount()) {
            return $customer->getPaymentAccount();
        } else {
            $newPaymentAccount = new CustomerPaymentAccount($customer);
            $customer->setPaymentAccount($newPaymentAccount);
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->persist($newPaymentAccount);
            $em->flush();

            return $newPaymentAccount;
        }
    }
}
