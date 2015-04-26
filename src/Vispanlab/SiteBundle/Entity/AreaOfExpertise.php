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
     * @ORM\Column (name="name", type="string")
     */
    protected $name;
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

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getConcepts() {
        return $this->concepts;
    }

    public function setConcepts($concepts) {
        $this->concepts = $concepts;
    }

    public function getSortedConcepts() {
        $concepts = $this->concepts->getIterator();
        $concepts->uasort(function ($a, $b) {
            $at = $this->stripGrAccent($a->getNameForLang('el'));
            $bt = $this->stripGrAccent($b->getNameForLang('el'));
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