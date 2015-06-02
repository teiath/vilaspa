<?php
namespace Vispanlab\SiteBundle\Entity\Exercise;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 */
class OnOff extends BaseExercise {
    /**
     * @ORM\Column (name="question", type="string", length=255)
     */
    protected $question;
    /**
     * @ORM\Column (name="answers", type="json")
     */
    protected $answers = array();

    public function __construct() {
        $this->answers = array(
            array('answer' => 've_on'),
            array('answer' => 've_off'),
        );
    }

    public function getQuestion() {
        return $this->question;
    }

    public function setQuestion($question) {
        $this->question = $question;
    }

    public function getAnswers() {
        return $this->answers;
    }

    public function setAnswers($answers) {
        $this->answers = $answers;
    }

    public function getCorrectAnswer() {
        foreach($this->answers as $i => $curAnswer) {
            if(isset($curAnswer['is_correct']) && $curAnswer['is_correct'] == true) {
                return ($i+1);
            }
        }
    }

    public function setCorrectAnswer($i) {
        $i = $i - 1;
        foreach($this->answers as &$curAnswer) {
            unset($curAnswer['is_correct']);
        }
        if(!isset($this->answers[$i])) { echo 'Ο αριθμός της σωστής απάντησης δεν υπάρχει'; die(); }
        $this->answers[$i]['is_correct'] = true;
    }
}
?>