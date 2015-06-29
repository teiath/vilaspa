<?php

namespace Vispanlab\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @Route("/cl/{aoe}/concept/{concept}", name="concept")
     * @ParamConverter("aoe", class="Vispanlab\SiteBundle\Entity\AreaOfExpertise", options={"repository_method" = "findOneByUrl"})
     * @Secure(roles="ROLE_USER")
     */
    public function concept(AreaOfExpertise $aoe, Concept $concept) {
        return $this->render('VispanlabSiteBundle:ConceptLibrary:concept.html.twig', array(
            'area_of_expertise' => $aoe,
            'concept' => $concept,
            'next' => $this->get('vispanlab.concept.service')->nextConcept($concept, $aoe, $this->getRequest()->getLocale()),
            'prev' => $this->get('vispanlab.concept.service')->prevConcept($concept, $aoe, $this->getRequest()->getLocale()),
        ));
    }

    /**
     * @Route("/search_concepts", name="search_concepts")
     * @Secure(roles="ROLE_USER")
     */
    public function searchConcepts() {
        $locale = $this->getRequest()->getLocale();
        $concepts = $this->container->get('doctrine')->getManager()->createQuery('SELECT c.id, def.text_formatted as value, aoe.url FROM Vispanlab\SiteBundle\Entity\Concept c JOIN c.name def WITH def.locale = \''.$locale.'\' JOIN c.areasofexpertise aoe WHERE def.text LIKE :searchTerm');
        $concepts->setParameter('searchTerm', '%'.$this->getRequest()->get('q').'%');
        $concepts = $concepts->execute();
        $concepts = array_map(function($c) {
            $c['value'] = str_replace('&amp;', '', strip_tags($c['value']));
            return $c;
        }, $concepts);
        return new JsonResponse($concepts);
    }
}
