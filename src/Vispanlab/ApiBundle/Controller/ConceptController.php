<?php

namespace Vispanlab\ApiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Vispanlab\ApiBundle\Controller\ApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use FOS\UserBundle\Model\UserInterface;

use Vispanlab\SiteBundle\Entity\AreaOfExpertise;

class ConceptController extends ApiController {
    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Get concepts for an area of expertise",
     *   output="Vispanlab\SiteBundle\Entity\Concept"
     * )
     * @ParamConverter("aoe", class="Vispanlab\SiteBundle\Entity\AreaOfExpertise", options={"repository_method" = "findOneByUrl"})
     * @Secure(roles="ROLE_USER")
     */
    public function getConceptsAction(AreaOfExpertise $aoe) {
        //$user = $this->container->get('security.context')->getToken()->getUser();
        $concepts = $this->container->get('vispanlab.paginator.extension')->paginate($aoe->getSortedConcepts($this->getRequest()->get('locale', 'el')), $this->getRequest()->get('limit', 10));
        return $this->api_response($concepts, 200);
    }
}