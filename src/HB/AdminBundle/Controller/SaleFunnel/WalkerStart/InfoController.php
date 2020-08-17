<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\WalkerStart;


use Symfony\Component\HttpFoundation\Response;

class InfoController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * InfoController constructor.
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
    public function handleAction(): Response
    {
        $content = $this->twig->render('@HBAdmin/SaleFunnel/WalkerStart/info.html.twig');

        return new Response($content);
    }
}
