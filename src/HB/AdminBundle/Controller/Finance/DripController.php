<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance;


use Symfony\Component\HttpFoundation\Response;

class DripController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * DripController constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @return Response
     */
    public function handleAction()
    {
        $content = $this->twig->render('@HBAdmin/Finance/drip.html.twig');

        return new Response($content);
    }


}