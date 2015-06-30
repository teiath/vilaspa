<?php
namespace Vispanlab\SiteBundle\Entity\Exercise;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 */
class ExamPaper extends BaseExercise {
    /**
     * @ORM\Column (name="exam_date", type="date")
     */
    protected $date;
    /**
     * @ORM\Column (name="exam_text", type="text")
     */
    protected $text;
    /**
     * @ORM\Column (name="exam_format_type", type="text")
     */
    public $format_type = 'richhtml';
    /**
     * @ORM\Column (name="exam_text_formatted", type="text")
     */
    public $text_formatted;

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getFormat_type() {
        return $this->format_type;
    }

    public function setFormat_type($format_type) {
        $this->format_type = $format_type;
    }

    public function getText_formatted() {
        return $this->text_formatted;
    }

    public function setText_formatted($text_formatted) {
        $this->text_formatted = $text_formatted;
    }

    public function getTextFormatted() {
        return $this->getText_formatted();
    }

    public function isAnswerCorrect($answer) { return true; }
}
?>