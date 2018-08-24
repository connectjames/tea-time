<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home_index")
     */
    public function indexAction()
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/about", name="home_about")
     */
    public function aboutAction()
    {
        return $this->render('home/about.html.twig');
    }

    /**
     * @Route("/contact", name="home_contact")
     */
    public function  contactAction()
    {
        return $this->render('home/contact.html.twig');
    }
}
