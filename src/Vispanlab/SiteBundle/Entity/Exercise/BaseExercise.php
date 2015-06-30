<?php
namespace Vispanlab\SiteBundle\Entity\Exercise;

use Doctrine\Common\Collections\ArrayCollection;
use Vispanlab\SiteBundle\Entity\SubjectArea;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Table(name="Exercise")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *  "multiple_choice" = "MultipleChoice",
 *  "on_off" = "OnOff",
 *  "solved" = "Solved",
 *  "matching" = "Matching",
 *  "exam_paper" = "ExamPaper"
 * })
 */
abstract class BaseExercise {
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column (name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Vispanlab\SiteBundle\Entity\SubjectArea", inversedBy="exercises")
     * @ORM\JoinColumn(name="subjectarea_id", referencedColumnName="id", onDelete="CASCADE")
     * @var SubjectArea
     */
    protected $subjectarea;
    const SIMPLE_EXERCISE = 1;
    const EVALUATION_TEST_EXERCISE = 2;
    const BOTH_EXERCISE = 3;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getSubjectarea() {
        return $this->subjectarea;
    }

    public function setSubjectarea(SubjectArea $subjectarea) {
        $this->subjectarea = $subjectarea;
    }

    public static function getShowInEvaluationTestChoices() {
        return array(
            self::SIMPLE_EXERCISE => 'Μόνο σε απλές ασκήσεις',
            self::EVALUATION_TEST_EXERCISE => 'Μόνο σε test αξιολόγησης',
            self::BOTH_EXERCISE => 'Και στα δύο',
        );
    }

    public abstract function isAnswerCorrect($answer);

    public function __toString() {
        if(!isset($this->question)) {
            return 'Νέα Ερώτηση';
        } else {
            return str_replace('<BR />', ' ', $this->question);
        }
    }
}
?>