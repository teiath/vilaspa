<?php
namespace Vispanlab\SiteBundle\Extension;

use Vispanlab\SiteBundle\Entity\Exercise\BaseExercise;
use Vispanlab\SiteBundle\Entity\AreaOfExpertise;
use Vispanlab\SiteBundle\Entity\SubjectArea;

class ExerciseService {
    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function getExercises(AreaOfExpertise $aoe, $type, SubjectArea $sa = null) {
        if($type != 'EvaluationTest') {
            if($sa != null) {
                $exercises = $this->em->getRepository('Vispanlab\SiteBundle\Entity\Exercise\\'.$type)->findBy(array(
                    'subjectarea' => $sa,
                ));
            } else {
                $exercises = array();
                foreach($aoe->getSubjectAreas() as $curSa) {
                    $exercises = array_merge($exercises, $this->em->getRepository('Vispanlab\SiteBundle\Entity\Exercise\\'.$type)->findBy(array(
                        'subjectarea' => $curSa,
                    )));
                }
            }
        } else {
            if($sa != null) {
                $exercises = $this->em->getRepository('Vispanlab\SiteBundle\Entity\Exercise\BaseExercise')->findBy(array(
                    'subjectarea' => $sa,
                ));
            } else {
                $exercises = array();
                foreach($aoe->getSubjectAreas() as $curSa) {
                    $exercises = array_merge($exercises, $this->em->getRepository('Vispanlab\SiteBundle\Entity\Exercise\BaseExercise')->findBy(array(
                        'subjectarea' => $curSa,
                    )));
                }
            }
        }
        if($type != 'Solved' && $type != 'Unsolved') { shuffle($exercises); }
        // Filter by showInEvaluationTest
        $exercises = array_filter($exercises, function($e) use ($type) {
            if($type != 'EvaluationTest') {
                if(!method_exists($e, 'getShowInEvaluationTest') || ($e->getShowInEvaluationTest() == BaseExercise::SIMPLE_EXERCISE || $e->getShowInEvaluationTest() == BaseExercise::BOTH_EXERCISE)) {
                    return true;
                }
            } else {
                if(method_exists($e, 'getShowInEvaluationTest') && ($e->getShowInEvaluationTest() == BaseExercise::EVALUATION_TEST_EXERCISE || $e->getShowInEvaluationTest() == BaseExercise::BOTH_EXERCISE)) {
                    return true;
                }
            }
            return false;
        });
        return $exercises;
    }
}
?>
