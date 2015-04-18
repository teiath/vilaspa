<?php

namespace Vilaspa\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Vilaspa\SiteBundle\Entity\AreaOfExpertise;

class ConceptLibraryController extends Controller {
    /**
     * @Route("/concept_library/{aoe}", name="concept_library")
     * @Secure(roles="ROLE_USER")
     */
    public function conceptLibrary(AreaOfExpertise $aoe) {
        return $this->render('VilaspaSiteBundle:ConceptLibrary:concept_library.html.twig', array(
            'area_of_expertise' => $aoe,
        ));
    }
}
