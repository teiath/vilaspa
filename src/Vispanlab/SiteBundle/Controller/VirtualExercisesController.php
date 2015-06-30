<?php

namespace Vispanlab\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Vispanlab\SiteBundle\Entity\Exercise\BaseExercise;
use Vispanlab\SiteBundle\Entity\AreaOfExpertise;
use Vispanlab\SiteBundle\Entity\SubjectArea;

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
     * @Route("/ve/{aoe}/{type}/{sa}", name="show_exercises", defaults={"sa" = null})
     * @ParamConverter("aoe", class="Vispanlab\SiteBundle\Entity\AreaOfExpertise", options={"repository_method" = "findOneByUrl"})
     * @ParamConverter("sa", class="Vispanlab\SiteBundle\Entity\SubjectArea", options={"repository_method" = "findOneByUrl"})
     * @Secure(roles="ROLE_USER")
     */
    public function showExercises(AreaOfExpertise $aoe, $type, SubjectArea $sa = null) {
        if($type != 'EvaluationTest') {
            if($sa != null) {
                $exercises = $this->container->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Exercise\\'.$type)->findBy(array(
                    'subjectarea' => $sa,
                ));
            } else {
                $exercises = array();
                foreach($aoe->getSubjectAreas() as $curSa) {
                    $exercises = array_merge($exercises, $this->container->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Exercise\\'.$type)->findBy(array(
                        'subjectarea' => $curSa,
                    )));
                }
            }
        } else {
            if($sa != null) {
                $exercises = $this->container->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Exercise\BaseExercise')->findBy(array(
                    'subjectarea' => $sa,
                ));
            } else {
                $exercises = $this->container->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Exercise\BaseExercise')->findAll();
            }
        }
        // Filter by showInEvaluationTest
        $exercises = array_filter($exercises, function($e) use ($type) {
            if($type != 'EvaluationTest') {
                if(method_exists($e, 'getShowInEvaluationTest') && ($e->getShowInEvaluationTest() == BaseExercise::SIMPLE_EXERCISE || $e->getShowInEvaluationTest() == BaseExercise::BOTH_EXERCISE)) {
                    return true;
                }
            } else {
                if(method_exists($e, 'getShowInEvaluationTest') && ($e->getShowInEvaluationTest() == BaseExercise::EVALUATION_TEST_EXERCISE || $e->getShowInEvaluationTest() == BaseExercise::BOTH_EXERCISE)) {
                    return true;
                }
            }
            return false;
        });
        // Pagination
        $paginator  = $this->get('knp_paginator');
        $exercises = $paginator->paginate(
            $exercises,
            $this->getRequest()->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        // End pagination
        return $this->render('VispanlabSiteBundle:VirtualExercises:show_exercises.html.twig', array(
            'area_of_expertise' => $aoe,
            'subject_area' => $sa,
            'exercises' => $exercises,
            'type' => $type,
        ));
    }

    /**
     * @Route("/ve_grade/{aoe}/{type}/{sa}", name="grade_exercises", defaults={"sa" = null})
     * @ParamConverter("aoe", class="Vispanlab\SiteBundle\Entity\AreaOfExpertise", options={"repository_method" = "findOneByUrl"})
     * @ParamConverter("sa", class="Vispanlab\SiteBundle\Entity\SubjectArea", options={"repository_method" = "findOneByUrl"})
     * @Secure(roles="ROLE_USER")
     */
    public function gradeExercises(AreaOfExpertise $aoe, $type, SubjectArea $sa = null) {
        $correctExercises = array();
        $wrongExercises = array();
        foreach($this->getRequest()->request->all() as $curId => $curAnswer) {
            $curExerciseId = substr($curId, 3);
            $curExercise = $this->container->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Exercise\BaseExercise')->find($curExerciseId);
            if(!$curExercise) { throw new \Exception('Exercise with id '.$curExerciseId.' not found for grading'); }
            if($curExercise->isAnswerCorrect($curAnswer)) { $correctExercises[] = $curExercise; } else { $wrongExercises[] = $curExercise; }
        }
        return $this->render('VispanlabSiteBundle:VirtualExercises:grade_exercises.html.twig', array(
            'area_of_expertise' => $aoe,
            'subject_area' => $sa,
            'correct_exercises' => $correctExercises,
            'wrong_exercises' => $wrongExercises,
            'type' => $type,
        ));
    }
}
