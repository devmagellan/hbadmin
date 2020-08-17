<?php

namespace HB\AuthorBundle\Controller\Index;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexControllerAlt
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * IndexController constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction()
    {
        // $content = $this->twig->render('@HBAuthor/Default/merchants-alt.html.twig');
        // $content = $this->twig->render('@HBAuthor/Default/hbnew.html.twig');
        $content = $this->twig->render('@HBAuthor/Default/merchants.html.twig');

        return new Response($content);
    }
}
