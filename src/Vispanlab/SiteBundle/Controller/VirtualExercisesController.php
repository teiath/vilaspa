<?php

namespace Vispanlab\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Vispanlab\SiteBundle\Entity\AreaOfExpertise;

class VirtualExercisesController extends Controller {
    /**
     * @Route("/ve/{aoe}", name="virtual_exercises")
     * @ParamConverter("aoe", class="Vispanlab\SiteBundle\Entity\AreaOfExpertise", options={"repository_method" = "findOneByUrl"})
     * @Secure(roles="ROLE_USER")
     */
    public function virtualExercises(AreaOfExpertise $aoe) {
        return $this->render('VispanlabSiteBundle:VirtualExercises:virtual_exercises.html.twig', array(
            'area_of_expertise' => $aoe,
        ));
    }

    /**
     * @Route("/ve/{aoe}/{sa}/{type}", name="show_exercises")
     * @ParamConverter("aoe", class="Vispanlab\SiteBundle\Entity\AreaOfExpertise", options={"repository_method" = "findOneByUrl"})
     * @ParamConverter("sa", class="Vispanlab\SiteBundle\Entity\AreaOfExpertise", options={"repository_method" = "findOneByUrl"})
     * @Secure(roles="ROLE_USER")
     */
    public function showExercises(AreaOfExpertise $aoe, SubjectArea $sa, $type) {
        $exercises = $this->container->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Exercise\\'.$type)->findBy(array(
            'subjectarea' => $sa,
        ));
        return $this->render('VispanlabSiteBundle:VirtualExercises:show_exercises.html.twig', array(
            'area_of_expertise' => $aoe,
            'subject_area' => $sa,
            'exercises' => $exercises,
        ));
    }
}
