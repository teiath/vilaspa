<?php
namespace Vispanlab\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 */
class Concept {
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column (name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="AreaOfExpertise", inversedBy="concepts")
     * @ORM\JoinColumn(name="areaofexpertise_id", referencedColumnName="id", onDelete="CASCADE")
     * @var AreaOfExpertise
     */
    protected $areaofexpertise;
    /**
     * @ORM\Column (name="name", type="string")
     */
    protected $name;
    /**
     * @ORM\Column (name="short_description", type="string")
     */
    protected $shortDescription;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getAreaofexpertise() {
        return $this->areaofexpertise;
    }

    public function setAreaofexpertise(AreaOfExpertise $areaofexpertise) {
        $this->areaofexpertise = $areaofexpertise;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getShortDescription() {
        return $this->shortDescription;
    }

    public function setShortDescription($shortDescription) {
        $this->shortDescription = $shortDescription;
    }

    public function __toString() {
        if(!isset($this->name)) {
            return 'Νέα Έννοια';
        } else {
            return $this->name;
        }
    }
}
?>