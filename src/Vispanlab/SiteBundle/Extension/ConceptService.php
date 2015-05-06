<?php
namespace Vispanlab\SiteBundle\Extension;

class ConceptService {
    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function nextConcept($concept, $locale) {
        $aoe = $concept->getAreaofexpertise();
        $allConcepts = $aoe->getSortedConcepts($locale);
        $index = $allConcepts->indexOf($concept);
        if($index) {
            $next = $allConcepts->get($index+1);
            if($next) { return $next; }
        }
        return null;
    }

    public function prevConcept($concept, $locale) {
        $aoe = $concept->getAreaofexpertise();
        $allConcepts = $aoe->getSortedConcepts($locale);
        $index = $allConcepts->indexOf($concept);
        if($index) {
            $prev = $allConcepts->get($index-1);
            if($prev) { return $prev; }
        }
        return null;
    }
}
?>
