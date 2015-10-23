<?php

namespace Vispanlab\SiteBundle\Controller;
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
            return $this->render('VispanlabSiteBundle:Default:index.html.twig', array(
                'csrf_token' => $csrfToken,
                'areasofexpertise' => $this->container->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\AreaOfExpertise')->findBy(array(), array('sortOrder' => 'ASC', 'id' => 'ASC')),
            ));
        } else {
            $usersCount = $this->container->get('doctrine')->getManager()->createQuery('SELECT COUNT(u) FROM Vispanlab\UserBundle\Entity\User u')->getSingleScalarResult();
            return $this->render('VispanlabSiteBundle:Default:index_logged_in.html.twig', array(
                'usersCount' => $usersCount,
            ));
        }
    }

    /**
     * @Route("/about", name="about_us")
     * @Template
     */
    public function aboutUsAction() {
        return $this->render('VispanlabSiteBundle::about_us.html.twig', array());
    }

    /**
     * @Route("/user_guide", name="user_guide")
     * @Template
     */
    public function userGuideAction() {
        return $this->render('VispanlabSiteBundle::user_guide.html.twig', array());
    }

    /**
     * @Route("/admin_guide", name="admin_guide")
     * @Template
     */
    public function adminGuideAction() {
        return $this->render('VispanlabSiteBundle::admin_guide.html.twig', array());
    }
}
