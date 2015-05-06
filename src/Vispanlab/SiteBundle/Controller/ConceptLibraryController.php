<?php

namespace Vispanlab\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Vispanlab\SiteBundle\Entity\AreaOfExpertise;
use Vispanlab\SiteBundle\Entity\Concept;

class ConceptLibraryController extends Controller {
    /**
     * @Route("/cl/{aoe}", name="concept_library")
     * @ParamConverter("aoe", class="Vispanlab\SiteBundle\Entity\AreaOfExpertise", options={"repository_method" = "findOneByUrl"})
     * @Secure(roles="ROLE_USER")
     */
    public function conceptLibrary(AreaOfExpertise $aoe) {
        return $this->render('VispanlabSiteBundle:ConceptLibrary:concept_library.html.twig', array(
            'area_of_expertise' => $aoe,
        ));
    }

    /**
     * @Route("/concept/{concept}", name="concept")
     * @Secure(roles="ROLE_USER")
     */
    public function concept(Concept $concept) {
        return $this->render('VispanlabSiteBundle:ConceptLibrary:concept.html.twig', array(
            'concept' => $concept,
            'next' => $this->get('vispanlab.concept.service')->nextConcept($concept, $this->getRequest()->getLocale()),
            'prev' => $this->get('vispanlab.concept.service')->prevConcept($concept, $this->getRequest()->getLocale()),
        ));
    }
}
