<?php
namespace Vispanlab\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Finder\Finder;
use Vispanlab\SiteBundle\Entity\Exercise\OnOff;
use Vispanlab\SiteBundle\Entity\Exercise\MultipleChoice;

class ImportVirtualExercisesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vispanlab:importve')
            ->setDescription('Import virtual exercises')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting ImportVirtualExercises process');
        $this->cvsParsingOptions = array(
            'ignoreFirstLine' => true
        );
        // Σωστό/Λάθος
        $xls = $this->parseCSV('C:\Users\Niral\Desktop\topografia\yliko\EEXA_01.07.2015,askPOLEO.xlsx', 0);
        $headersRow = $xls->getRowIterator(2)->current();
        $headers = $this->parseHeadersToArray($headersRow);
        foreach ($xls->getRowIterator(3) as $row) {
            $fields = $this->parseRowToArray($row, $headers);
            if(!isset($fields['ΕΡΩΤΗΣΗ'])) { $output->writeln('Empty question. Skipping.'); continue; }
            $exercise = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Exercise\OnOff')->findOneBy(array(
                'question' => $fields['ΕΡΩΤΗΣΗ'],
            ));
            if($exercise) { $output->writeln('Exercise '.$fields['ΕΡΩΤΗΣΗ'].' already exists. Skipping.'); continue; }
            $exercise = new OnOff();
            if($fields['ΘΕΜΑΤΙΚΗ ΕΝΟΤΗΤΑ'] != null) {
                $subjectArea = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\SubjectArea')->findOneBy(array(
                    'nameEl' => $fields['ΘΕΜΑΤΙΚΗ ΕΝΟΤΗΤΑ'],
                ));
                if(!$subjectArea) { throw new \Exception('Subject Area not found'); }
            } else {
                $subjectArea = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\SubjectArea')->findOneBy(array(
                    'nameEl' => 'ΠΟΛΕΟΔΟΜΙΑ',
                ));
            }
            $exercise->setSubjectarea($subjectArea);
            $exercise->setShowInEvaluationTest(3);
            $exercise->setQuestion($fields['ΕΡΩΤΗΣΗ']);
            $exercise->setCorrectAnswer($fields['ΣΩΣΤΟ/ΛΑΘΟΣ'] == 'Σ' ? '1' : '2');
            $this->getContainer()->get('doctrine')->getManager()->persist($exercise);
            $this->getContainer()->get('doctrine')->getManager()->flush($exercise);
            $output->writeln('Added '.$fields['ΕΡΩΤΗΣΗ']);
        }
        // Πολλαπλής
        $xls = $this->parseCSV('C:\Users\Niral\Desktop\topografia\yliko\EEXA_01.07.2015,askPOLEO.xlsx', 1);
        $headersRow = $xls->getRowIterator(1)->current();
        $headers = $this->parseHeadersToArray($headersRow);
        foreach ($xls->getRowIterator(2) as $row) {
            $fields = $this->parseRowToArray($row, $headers);
            if(!isset($fields['ΕΡΩΤΗΣΗ'])) { $output->writeln('Empty question. Skipping.'); continue; }
            $exercise = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Exercise\MultipleChoice')->findOneBy(array(
                'question' => $fields['ΕΡΩΤΗΣΗ'],
            ));
            if($exercise) { $output->writeln('Exercise '.$fields['ΕΡΩΤΗΣΗ'].' already exists. Skipping.'); continue; }
            $exercise = new MultipleChoice();
            if($fields['ΘΕΜΑΤΙΚΗ ΕΝΟΤΗΤΑ'] != null) {
                $subjectArea = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\SubjectArea')->findOneBy(array(
                    'nameEl' => $fields['ΘΕΜΑΤΙΚΗ ΕΝΟΤΗΤΑ'],
                ));
                if(!$subjectArea) { throw new \Exception('Subject Area not found'); }
            } else {
                $subjectArea = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\SubjectArea')->findOneBy(array(
                    'nameEl' => 'ΠΟΛΕΟΔΟΜΙΑ',
                ));
            }
            $exercise->setSubjectarea($subjectArea);
            $exercise->setShowInEvaluationTest(3);
            $exercise->setQuestion($fields['ΕΡΩΤΗΣΗ']);
            $answers = array();
            for($i = 1; i <= 5; $i++) {
                $answers[] = array(
                    'answer' => $fields['ΑΠΑΝΤΗΣΗ '.$i],
                );
            }
            $exercise->setAnswers($answers);
            $exercise->setCorrectAnswer($fields['ΣΩΣΤΗ ΑΠΑΝΤΗΣΗ']);
            $this->getContainer()->get('doctrine')->getManager()->persist($exercise);
            $this->getContainer()->get('doctrine')->getManager()->flush($exercise);
            $output->writeln('Added '.$fields['ΕΡΩΤΗΣΗ']);
        }
        $output->writeln('Completed ImportWord process');
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

    private function parseCSV($file, $sheetNum)
    {
        $ignoreFirstLine = $this->cvsParsingOptions['ignoreFirstLine'];

        $finder = new Finder();
        $finder->files()->in(dirname($file))->name(basename($file));
        ;
        foreach ($finder as $file) { $csv = $file; }

        $phpExcelObject = $this->getContainer()->get('xls.load_xls2007')->load($csv->getRealPath());
        $sheet = $phpExcelObject->getSheet($sheetNum);
        //$objReader = PHPExcel_IOFactory::createReader($inputFileType);
        return $sheet;
    }
}