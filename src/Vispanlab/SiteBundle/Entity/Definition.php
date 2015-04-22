<?php
namespace Vispanlab\SiteBundle\Entity;

use Vispanlab\CommonBundle\Entity\ImageFile;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 */
class Definition {
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column (name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column (name="locale", type="string", length=5)
     */
    protected $locale = 'el';
    /**
     * @ORM\Column (name="text", type="text")
     */
    protected $text;
    /**
     * @ORM\ManyToOne(targetEntity="Concept", inversedBy="name")
     * @ORM\JoinColumn(name="concept_name_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Concept
     */
    protected $conceptAsName;
    /**
     * @ORM\ManyToOne(targetEntity="Concept", inversedBy="definition")
     * @ORM\JoinColumn(name="concept_definition_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Concept
     */
    protected $conceptAsDefinition;
    /**
     * @ORM\ManyToOne(targetEntity="Concept", inversedBy="alternativeDefinition")
     * @ORM\JoinColumn(name="concept_alternative_definition_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Concept
     */
    protected $conceptAsAlternativeDefinition;
    /**
     * @ORM\ManyToOne(targetEntity="Concept", inversedBy="relatedConcept")
     * @ORM\JoinColumn(name="concept_related_concept_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Concept
     */
    protected $conceptAsRelatedConcept;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLocale() {
        return $this->locale;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getConceptAsName() {
        return $this->conceptAsName;
    }

    public function setConceptAsName(Concept $conceptAsName) {
        $this->conceptAsName = $conceptAsName;
    }

    public function getConceptAsDefinition() {
        return $this->conceptAsDefinition;
    }

    public function setConceptAsDefinition(Concept $conceptAsDefinition) {
        $this->conceptAsDefinition = $conceptAsDefinition;
    }

    public function getConceptAsAlternativeDefinition() {
        return $this->conceptAsAlternativeDefinition;
    }

    public function setConceptAsAlternativeDefinition(Concept $conceptAsAlternativeDefinition) {
        $this->conceptAsAlternativeDefinition = $conceptAsAlternativeDefinition;
    }

    public function getConceptAsRelatedConcept() {
        return $this->conceptAsRelatedConcept;
    }

    public function setConceptAsRelatedConcept(Concept $conceptAsRelatedConcept) {
        $this->conceptAsRelatedConcept = $conceptAsRelatedConcept;
    }
}
?>