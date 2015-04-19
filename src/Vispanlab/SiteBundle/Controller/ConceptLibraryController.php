<?php

namespace Vispanlab\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Vispanlab\SiteBundle\Entity\AreaOfExpertise;

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
}
