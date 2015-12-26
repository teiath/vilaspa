<?php
namespace Vispanlab\AdminBundle\Extension;

use Vispanlab\SiteBundle\Entity\Exercise\BaseExercise;
use Vispanlab\SiteBundle\Entity\Exercise\OnOff;
use Vispanlab\SiteBundle\Entity\Exercise\MultipleChoice;
use Vispanlab\SiteBundle\Entity\Exercise\Matching;
use Vispanlab\SiteBundle\Entity\Exercise\Solved;
use Vispanlab\SiteBundle\Entity\Exercise\Unsolved;

class VirtualExercisesImporter {
    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function importVirtualExercise(\PHPExcel_Worksheet $objWorkSheet, $class) {
        if($class === 'Vispanlab\SiteBundle\Entity\Exercise\OnOff') {
            return $this->importOnOff($objWorkSheet);
        } else if($class === 'Vispanlab\SiteBundle\Entity\Exercise\MultipleChoice') {
            return $this->importMultipleChoice($objWorkSheet);
        }
    }

    // Πολλαπλής
    private function importMultipleChoice(\PHPExcel_Worksheet $xls) {
        $headersRow = $xls->getRowIterator(1)->current();
        $headers = $this->parseHeadersToArray($headersRow);
        $toFlush = array();
        foreach($xls->getRowIterator(2) as $row) {
            $fields = $this->parseRowToArray($row, $headers);
            $exercise = $this->findExercise($fields, $row, 'Vispanlab\SiteBundle\Entity\Exercise\MultipleChoice');
            $sanswers = explode("\n", $fields['answers']);
            $answers = array();
            $correctAnswer = -1;
            foreach($sanswers as $i => $curAnswer) {
                if(strpos($curAnswer, '[x]') !== false) {
                    $correctAnswer = $i+1;
                }
                $answers[] = array(
                    'answer' => str_replace('[x]', '', $curAnswer)
                );
            }
            if($correctAnswer < 0) { throw new \Exception('No correct answer specified!'); }
            $exercise->setAnswers($answers);
            $exercise->setCorrectAnswer($correctAnswer);
            $this->em->persist($exercise);
            $toFlush[] = $exercise;
        }
        $this->em->flush($toFlush);
    }

    // Σωστό/Λάθος
    private function importOnOff(\PHPExcel_Worksheet $xls) {
        $headersRow = $xls->getRowIterator(1)->current();
        $headers = $this->parseHeadersToArray($headersRow);
        $toFlush = array();
        foreach($xls->getRowIterator(2) as $row) {
            $fields = $this->parseRowToArray($row, $headers);
            $exercise = $this->findExercise($fields, $row, 'Vispanlab\SiteBundle\Entity\Exercise\OnOff');
            $sanswers = explode("\n", $fields['answers']);
            $correctAnswer = -1;
            foreach($sanswers as $i => $curAnswer) {
                if(strpos($curAnswer, '[x]') !== false) {
                    $correctAnswer = $i+1;
                }
            }
            if($correctAnswer < 0) { throw new \Exception('No correct answer specified!'); }
            $exercise->setCorrectAnswer($correctAnswer);
            $this->em->persist($exercise);
            $toFlush[] = $exercise;
        }
        $this->em->flush($toFlush);
    }

    private function parseHeadersToArray($headersRow) {
        $cellIterator = $headersRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $result = array();
        foreach ($cellIterator as $cell) {
            $result[] = $cell->getValue();
        }
        return $result;
    }

    private function parseRowToArray($row, $headers) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $result = array();
        $i = 0;
        foreach ($cellIterator as $cell) {
            $result[$headers[$i]] = $cell->getValue();
            $i++;
        }
        return $result;
    }

    private function findExercise($fields, \PHPExcel_Worksheet_Row $row, $class) {
        $rowIndex = $row->getRowIndex();
        if(!isset($fields['question'])) { throw new \Exception('Question not defined in row '.$rowIndex); }
        if(isset($fields['id']) && $fields['id'] != '') {
            $exercise = $this->em->getRepository($class)->find($fields['id']);
        } else {
            $exercise = $this->em->getRepository($class)->findOneBy(array(
                'question' => $fields['question'],
            ));
        }
        if(!$exercise) { $exercise = new $class(); }
        $subjectArea = $this->em->getRepository('Vispanlab\SiteBundle\Entity\SubjectArea')->findOneBy(array(
            'nameEl' => str_replace('&newline;', '<BR />', $fields['subjectarea']),
        ));
        if(!$subjectArea) { throw new \Exception('Subject Area not found in row '.$rowIndex); }
        $exercise->setSubjectarea($subjectArea);
        $exercise->setQuestion($fields['question']);
        if(isset($fields['showInEvaluationTest']) && $fields['showInEvaluationTest'] != '') { $exercise->setShowInEvaluationTest($fields['showInEvaluationTest']); }
        return $exercise;
    }
}
?>
