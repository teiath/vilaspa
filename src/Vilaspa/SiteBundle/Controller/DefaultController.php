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
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
            return $this->render('VilaspaSiteBundle:Default:index.html.twig', array(
                'csrf_token' => $csrfToken,
            ));
        } else {
            return $this->render('VilaspaSiteBundle:Default:index_logged_in.html.twig', array());
        }
    }
}
