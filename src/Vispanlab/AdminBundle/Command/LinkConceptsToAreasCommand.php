<?php
namespace Vispanlab\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Sunra\PhpSimple\HtmlDomParser;
use Vispanlab\SiteBundle\Entity\AreaOfExpertise;
use Vispanlab\SiteBundle\Entity\Concept;

class LinkConceptsToAreasCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vispanlab:linkconceptstoareas')
            ->setDescription('Import word data')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting LinkConceptsToAreasCommand process');
        $htmlStr = file_get_contents('C:\Users\Niral\Desktop\topografia\yliko\html\concepts_to_areas.html');
        $dom = HtmlDomParser::str_get_html( $htmlStr );

        // TODO fetch actual gnwstiko antikeimno instead of default
        $poleodomia = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\AreaOfExpertise')->find(1);
        $xorotaxia = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\AreaOfExpertise')->find(2);
        $ktimatologio = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\AreaOfExpertise')->find(3);

        foreach($dom->find('table tr') as $curTr) {
            $conceptName = $curTr->find('td', 1);
            $definition = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Definition')->findOneBy(array(
                'text' => $this->mb_trim($conceptName->plaintext, ' '.PHP_EOL)
            ));
            if(!isset($definition)) {
                $output->writeln('Could not find concept: '.$this->mb_trim($conceptName->plaintext, ' '.PHP_EOL));
                continue;
            }
            $concept = $definition->getConcept();
            $inPoleodomia = mb_strlen($this->mb_trim($curTr->find('td', 2)->plaintext, ' '.PHP_EOL)) > 0;
            $inXorotaxia = mb_strlen($this->mb_trim($curTr->find('td', 3)->plaintext, ' '.PHP_EOL)) > 0;
            $inKtimatologio = mb_strlen($this->mb_trim($curTr->find('td', 4)->plaintext, ' '.PHP_EOL)) > 0;
            if($inPoleodomia) {
                if($concept->getAreaofexpertise() == $poleodomia) { $output->writeln('Concept '.$concept->getNameForLang('el').' already in correct area of expertise'); } else {
                    $concept = $this->setSafeAreaofexpertise($concept, $poleodomia);
                }
            }
            if($inXorotaxia) {
                if($concept->getAreaofexpertise() == $xorotaxia) { $output->writeln('Concept '.$concept->getNameForLang('el').' already in correct area of expertise'); } else {
                    $concept = $this->setSafeAreaofexpertise($concept, $xorotaxia);
                }
            }
            if($inKtimatologio) {
                if($concept->getAreaofexpertise() == $ktimatologio) { $output->writeln('Concept '.$concept->getNameForLang('el').' already in correct area of expertise'); } else {
                    $concept = $this->setSafeAreaofexpertise($concept, $ktimatologio);
                }
            }
            //$concept->setAreaofexpertise($poleodomia);
            $this->getContainer()->get('doctrine')->getManager()->persist($concept);
            $this->getContainer()->get('doctrine')->getManager()->flush($concept);
            $output->writeln('Linked concept: '.$concept->getNameForLang('el'));
        }
        $output->writeln('Completed LinkConceptsToAreasCommand process');
    }

    private function setSafeAreaofexpertise(Concept &$concept, AreaOfExpertise &$areaofexpertise) {
        if($concept->getAreaofexpertise() == null) {
            $concept->setAreaofexpertise($areaofexpertise);
            return $concept;
        } else {
            $newConcept = clone $concept;
            $newConcept->setAreaofexpertise($areaofexpertise);
            foreach($concept->getName() as $curName) {
                $new = clone $curName;
                $new->setConceptAsName($newConcept);
                $newConcept->getName()->add($new);
                $this->getContainer()->get('doctrine')->getManager()->persist($newConcept);
            }
            foreach($concept->getDefinition() as $curDefinition) {
                $new = clone $curDefinition;
                $new->setConceptAsDefinition($newConcept);
                $newConcept->getDefinition()->add($new);
                $this->getContainer()->get('doctrine')->getManager()->persist($newConcept);
            }
            foreach($concept->getAlternativeDefinitions() as $curAlternativeDefinition) {
                $new = clone $curAlternativeDefinition;
                $new->setConceptAsAlternativeDefinition($newConcept);
                $newConcept->getAlternativeDefinitions()->add($new);
                $this->getContainer()->get('doctrine')->getManager()->persist($newConcept);
            }
            foreach($concept->getRelatedConcepts() as $curRelatedConcept) {
                $new = clone $curRelatedConcept;
                $new->setConceptAsRelatedConcept($newConcept);
                $newConcept->getRelatedConcepts()->add($new);
                $this->getContainer()->get('doctrine')->getManager()->persist($newConcept);
            }
            return $newConcept;
        }
    }

    // Util functions
    private function mb_trim ($string, $charlist = null) {
        $charlist = preg_quote ($charlist, '/');

        if (is_null($charlist)) {
            return trim ($string);
        } else {
            return preg_replace ("/(^[$charlist]+)|([$charlist]+$)/us", '', $string);
        }
    }
}