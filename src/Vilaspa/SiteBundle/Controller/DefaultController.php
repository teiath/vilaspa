<?php

namespace Vilaspa\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller {
    /**
     * @Route("/", name="home")
     * @Template
     */
    public function indexAction() {
        return $this->render('VilaspaSiteBundle:Default:index.html.twig', array());
    }
}
