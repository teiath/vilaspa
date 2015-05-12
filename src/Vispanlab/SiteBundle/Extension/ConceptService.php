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
        if($index !== false) {
            if($index == $allConcepts->count() - 1) {
                $next = $allConcepts->first();
            } else {
                $next = $allConcepts->get($index+1);
            }
            if($next) { return $next; }
        }
        return null;
    }

    public function prevConcept($concept, $locale) {
        $aoe = $concept->getAreaofexpertise();
        $allConcepts = $aoe->getSortedConcepts($locale);
        $index = $allConcepts->indexOf($concept);
        if($index !== false) {
            if($index == 0) {
                $prev = $allConcepts->last();
            } else {
                $prev = $allConcepts->get($index-1);
            }
            if($prev) { return $prev; }
        }
        return null;
    }
}
?>
