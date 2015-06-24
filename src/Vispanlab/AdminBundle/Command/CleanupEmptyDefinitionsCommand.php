<?php
namespace Vispanlab\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupEmptyDefinitionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vispanlab:cleanupemptydefinitions')
            ->setDescription('Find users who are in the delivery polygon')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting CleanupEmptyDefinitions process');
        //$q = $this->getContainer()->get('doctrine')->getManager()->createQuery("SELECT u FROM Vispanlab\SiteBundle\Entity\Definition u JOIN u.conceptAsAlternativeDefinition c WHERE c.id = 732");
        $q = $this->getContainer()->get('doctrine')->getManager()->createQuery("SELECT u FROM Vispanlab\SiteBundle\Entity\Definition u");
        $iterableResult = $q->iterate();
        while (($row = $iterableResult->next()) !== false) {
            //var_dump(str_replace(' ', '', strip_tags($row[0]->getText_formatted())));
            if(strlen(str_replace(' ', '', strip_tags($row[0]->getText_formatted()))) < 3 ||
                str_replace(' ', '', strip_tags($row[0]->getText_formatted())) == 'ΕΝΑΛΛΑΚΤΙΚΟΣ/ΟΙΟΡΙΣΜΟΣ/ΟΙΠΗΓΗ:' ||
                str_replace(' ', '', strip_tags($row[0]->getText_formatted())) == 'DEFINITIONSOURCE:' ||
                str_replace(' ', '', strip_tags($row[0]->getText_formatted())) == 'ΣΥΝΑΦΕΙΣΕΝΝΟΙΕΣ:' ||
                str_replace(' ', '', strip_tags($row[0]->getText_formatted())) == 'ΕΙΚΟΝΙΚΗ ΑΝΑΠΑΡΑΣΤΑΣΗ' ||
                str_replace(' ', '', strip_tags($row[0]->getText_formatted())) == 'ΕΙΚΟΝΙΚΗ ΑΝΑΠΑΡΑΣΤΑΣΗΟΧΙ' ||
                str_replace(' ', '', strip_tags($row[0]->getText_formatted())) == 'ΠΑΡΑΤΗΡΗΣΕΙΣ') {
                $output->writeln("Removing ".$row[0]->getId().' '.trim(strip_tags($row[0]->getText_formatted())));
                $this->getContainer()->get('doctrine')->getManager()->remove($row[0]);
                $this->getContainer()->get('doctrine')->getManager()->flush($row[0]);
            }
            $this->getContainer()->get('doctrine')->getManager()->detach($row[0]); // detach from Doctrine, so that it can be GC'd immediately
        }
    }
}