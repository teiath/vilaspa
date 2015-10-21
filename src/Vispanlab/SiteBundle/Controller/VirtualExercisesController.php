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
        $exercises = $this->container->get('vispanlab.exercise.service')->getExercises($aoe, $type, $sa);
        // Pagination
        $itemsPerPage = 10;
        if($type == 'EvaluationTest') {
            $itemsPerPage = 5;
        } else if($type == 'Unsolved' || $type == 'Solved') {
            $itemsPerPage = 1;
        }
        $paginator  = $this->get('knp_paginator');
        $exercises = $paginator->paginate(
            $exercises,
            $this->getRequest()->query->getInt('page', 1)/*page number*/,
            $itemsPerPage/*limit per page*/
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
     * @Route("/ve_search/{aoe}", name="search_exercises")
     * @ParamConverter("aoe", class="Vispanlab\SiteBundle\Entity\AreaOfExpertise", options={"repository_method" = "findOneByUrl"})
     * @Secure(roles="ROLE_USER")
     */
    public function searchExercises(AreaOfExpertise $aoe) {
        $exercises = $this->container->get('doctrine')->getManager()->createQuery(
            'SELECT DISTINCT e
            FROM Vispanlab\SiteBundle\Entity\Exercise\BaseExercise e
            JOIN e.subjectarea sa
            JOIN e.relatedConcepts r JOIN r.name n
            WHERE sa.areaofexpertise = :aoe AND (n.text LIKE :q)'
        )
        ->setParameter('aoe', $aoe)
        ->setParameter('q', '%'.$this->getRequest()->get('q').'%')
        ->getResult();
        // Filter by showInEvaluationTest
        $exercises = array_filter($exercises, function($e) {
            if(method_exists($e, 'getShowInEvaluationTest') && ($e->getShowInEvaluationTest() == BaseExercise::SIMPLE_EXERCISE || $e->getShowInEvaluationTest() == BaseExercise::BOTH_EXERCISE)) {
                return true;
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
            'subject_area' => null,
            'exercises' => $exercises,
            'type' => 'SearchExercise',
            'search_term' => $this->getRequest()->get('q'),
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
        $exercises = array();
        $user = $this->container->get('security.context')->getToken()->getUser();
        foreach($this->getRequest()->request->all() as $curId => $curAnswer) {
            $curExerciseId = substr($curId, 3);
            $curExercise = $this->container->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Exercise\BaseExercise')->find($curExerciseId);
            if(!$curExercise) { throw new \Exception('Exercise with id '.$curExerciseId.' not found for grading'); }
            if($curExercise->isAnswerCorrect($curAnswer)) {
                $correctExercises[] = $curExercise;
                $user->setScore($user->getScore() + 1);
            } else {
                $wrongExercises[] = $curExercise;
                $user->setScore($user->getScore() - 1);
            }
            $exercises[] = $curExercise;
        }
        $this->container->get('doctrine')->getManager()->persist($user);
        $this->container->get('doctrine')->getManager()->flush($user);
        $rank = $this->container->get('doctrine')->getManager()->createQuery('SELECT COUNT(u) AS rank FROM Vispanlab\UserBundle\Entity\User u WHERE u.score>=:score')->setParameter('score', $user->getScore())->getSingleScalarResult();
        $topUser = $this->container->get('doctrine')->getManager()->createQuery('SELECT u FROM Vispanlab\UserBundle\Entity\User u ORDER BY u.score DESC')->setMaxResults(1)->getResult();
        $requiredScore = $this->container->get('doctrine')->getManager()->createQuery('SELECT u.score AS rank FROM Vispanlab\UserBundle\Entity\User u WHERE u.score>:score')->setParameter('score', $user->getScore())->setMaxResults(1)->getSingleScalarResult();
        return $this->render('VispanlabSiteBundle:VirtualExercises:grade_exercises.html.twig', array(
            'area_of_expertise' => $aoe,
            'subject_area' => $sa,
            'exercises' => $exercises,
            'correct_exercises' => $correctExercises,
            'wrong_exercises' => $wrongExercises,
            'type' => $type,
            'rank' => $rank,
            'topUser' => $topUser[0],
            'requiredScore' => $requiredScore,
        ));
    }
}
