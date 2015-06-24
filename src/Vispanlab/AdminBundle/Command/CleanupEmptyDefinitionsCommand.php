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
        $q = $this->getContainer()->get('doctrine')->getManager()->createQuery("SELECT u FROM Vispanlab\SiteBundle\Entity\Definition u");
        $iterableResult = $q->iterate();
        while (($row = $iterableResult->next()) !== false) {
            if(strlen(trim(strip_tags($row[0]->getText_formatted()))) < 3) {
                $output->writeln("Removing ".$row[0]->getId().' '.trim(strip_tags($row[0]->getText_formatted())));
                $this->getContainer()->get('doctrine')->getManager()->remove($row[0]);
                $this->getContainer()->get('doctrine')->getManager()->flush($row[0]);
            }
            $this->getContainer()->get('doctrine')->getManager()->detach($row[0]); // detach from Doctrine, so that it can be GC'd immediately
        }
    }
}