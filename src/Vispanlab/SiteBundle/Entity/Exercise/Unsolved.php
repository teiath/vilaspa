<?php
namespace Vispanlab\SiteBundle\Entity\Exercise;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 */
class Unsolved extends BaseExercise {
    /**
     * @ORM\Column (name="goal", type="string", length=255)
     */
    protected $goal;
    /**
     * @ORM\Column (name="question", type="string", length=255)
     */
    protected $question;
    /**
     * @ORM\Column (name="data", type="string", length=255)
     */
    protected $data;
    /**
     * @ORM\Column (name="requested", type="string", length=255)
     */
    protected $requested;

    function getGoal() {
        return $this->goal;
    }

    function setGoal($goal) {
        $this->goal = $goal;
    }

    public function getQuestion() {
        return $this->question;
    }

    public function setQuestion($question) {
        $this->question = $question;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getRequested() {
        return $this->requested;
    }

    public function setRequested($requested) {
        $this->requested = $requested;
    }

    public function isAnswerCorrect($answer) { return true; }
}
?>