<?php
namespace Vispanlab\SiteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 */
class AreaOfExpertise {
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column (name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column (name="url", type="string", unique=true, length=20)
     */
    protected $url;
    /**
     * @ORM\Column (name="name_el", type="string")
     */
    protected $nameEl;
    /**
     * @ORM\Column (name="name_en", type="string")
     */
    protected $nameEn;
    /**
     * @ORM\OneToMany(targetEntity="Concept", mappedBy="areaofexpertise")
     */
    protected $concepts;

    public function __construct() {
        $this->name = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getNameEl() {
        return $this->nameEl;
    }

    public function setNameEl($nameEl) {
        $this->nameEl = $nameEl;
    }

    public function getNameEn() {
        return $this->nameEn;
    }

    public function setNameEn($nameEn) {
        $this->nameEn = $nameEn;
    }

    public function getName($locale) {
        if($locale == 'el') { return $this->nameEl; }
        else return $this->nameEn;
    }

    public function getConcepts($locale) {
        $concepts = $this->concepts;
        $concepts = $concepts->filter(function(Concept $concept) use ($locale) {
            if($concept->hasNameForLang($locale)) { return true; }
            else return false;
        });
        return $concepts;
    }

    public function setConcepts($concepts) {
        $this->concepts = $concepts;
    }

    public function getSortedConcepts($locale) {
        $concepts = $this->getConcepts($locale)->getIterator();
        $concepts->uasort(function ($a, $b) use ($locale) {
            $at = $this->stripGrAccent($a->getNameForLang($locale)->getText_formatted());
            $bt = $this->stripGrAccent($b->getNameForLang($locale)->getText_formatted());
            if($at == $bt) { return 0; }
            return ($at > $bt) ? 1 : -1;
        });
        return new ArrayCollection(iterator_to_array($concepts));
    }

    private function stripGrAccent($tempName) {
      $tempName = strtr($tempName, "ΆάΈέΉήΌόΎύΏώ", "ααεεηηοουυωω");
      return strtr($tempName, "αβγδεζηθικλμνξοπρστυφχψως" , "ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩΣ");
    }

    public function __toString() {
        if(!isset($this->name)) {
            return 'Νέο Γνωστικό Αντικείμενο';
        } else {
            return $this->name;
        }
    }
}
?>