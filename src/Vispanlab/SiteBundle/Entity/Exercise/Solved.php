<?php
namespace Vispanlab\SiteBundle\Entity\Exercise;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 */
class Solved extends BaseExercise {
    /**
     * @ORM\Column (name="goal", type="string", length=255)
     */
    protected $goal;
    /**
     * @ORM\Column (name="question", type="string", length=255)
     */
    protected $question;
    /**
     * @ORM\Column (name="data", type="text")
     */
    protected $data;
    /**
     * @ORM\Column (name="requested", type="text")
     */
    protected $requested;
    /**
     * @ORM\Column (name="methodology", type="text")
     */
    protected $methodology;
    /**
     * @ORM\Column (name="algorithm", type="text")
     */
    protected $algorithm;
    /**
     * @ORM\Column (name="solution", type="text")
     */
    protected $solution;
    /**
     * @ORM\Column (name="result", type="text")
     */
    protected $result;

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

    public function getMethodology() {
        return $this->methodology;
    }

    public function setMethodology($methodology) {
        $this->methodology = $methodology;
    }

    function getAlgorithm() {
        return $this->algorithm;
    }

    function setAlgorithm($algorithm) {
        $this->algorithm = $algorithm;
    }

    public function getSolution() {
        return $this->solution;
    }

    public function setSolution($solution) {
        $this->solution = $solution;
    }

    public function getResult() {
        return $this->result;
    }

    public function setResult($result) {
        $this->result = $result;
    }

    public function isAnswerCorrect($answer) { return true; }
}
?>