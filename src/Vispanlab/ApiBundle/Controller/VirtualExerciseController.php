<?php

namespace Vispanlab\ApiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Vispanlab\ApiBundle\Controller\ApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use FOS\UserBundle\Model\UserInterface;

use Vispanlab\SiteBundle\Entity\Exercise\BaseExercise;
use Vispanlab\SiteBundle\Entity\AreaOfExpertise;
use Vispanlab\SiteBundle\Entity\SubjectArea;

class VirtualExerciseController extends ApiController {
    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Get virtual exercises of specific type for an area of expertise. They can also be optionally filtered by a subject area. Supported types are: OnOff, MultipleChoice, Matching, Solved and Unsolved.",
     *  filters={
     *      {"name"="type", "dataType"="string"},
     *      {"name"="sa", "dataType"="string"},
     *  },
     *   output="Vispanlab\SiteBundle\Entity\Exercise\BaseExercise"
     * )
     * @ParamConverter("aoe", class="Vispanlab\SiteBundle\Entity\AreaOfExpertise", options={"repository_method" = "findOneByUrl"})
     * @Secure(roles="ROLE_USER")
     */
    public function getExercisesAction(AreaOfExpertise $aoe) {
        //$user = $this->container->get('security.context')->getToken()->getUser();
        if($this->getRequest()->get('sa') != null) {
            $sa = $this->container->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\SubjectArea')->findOneByUrl($this->getRequest()->get('sa'));
        } else {
            $sa = null;
        }
        if($this->getRequest()->get('type') == null) { return $this->api_error('The type parameter must be specified', 404); }
        if($this->getRequest()->get('sa') != null && !isset($sa)) { return $this->api_error('Subject area '.$this->getRequest()->get('sa').' not found', 404); }
        $exercises = $this->container->get('vispanlab.exercise.service')->getExercises($aoe, $this->getRequest()->get('type'), $sa);
        $exercises = $this->container->get('vispanlab.paginator.extension')->paginate($exercises, $this->getRequest()->get('limit', 10));
        return $this->api_response($exercises, 200);
    }
}