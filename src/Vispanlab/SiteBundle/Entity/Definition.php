<?php
namespace Vispanlab\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Exclude;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *  @ORM\UniqueConstraint(name="locale_concept_name_id", columns={"locale", "concept_name_id"}),
 *  @ORM\UniqueConstraint(name="locale_concept_definition_id", columns={"locale", "concept_definition_id"}),
 *  @ORM\UniqueConstraint(name="locale_concept_alternative_definition_id", columns={"locale", "concept_alternative_definition_id"}),
 *  @ORM\UniqueConstraint(name="locale_concept_related_concept_id", columns={"locale", "concept_related_concept_id"}),
 *  @ORM\UniqueConstraint(name="locale_concept_media_id", columns={"locale", "concept_media_id"}),
 * })
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
     * @Exclude
     */
    protected $text;
    /**
     * @ORM\Column (name="format_type", type="text")
     * @Exclude
     */
    public $format_type = 'richhtml';
    /**
     * @ORM\Column (name="text_formatted", type="text")
     */
    public $text_formatted;
    /**
     * @ORM\ManyToOne(targetEntity="Concept", inversedBy="name")
     * @ORM\JoinColumn(name="concept_name_id", referencedColumnName="id", onDelete="CASCADE")
     * @Exclude
     * @var Concept
     */
    protected $conceptAsName;
    /**
     * @ORM\ManyToOne(targetEntity="Concept", inversedBy="definition")
     * @ORM\JoinColumn(name="concept_definition_id", referencedColumnName="id", onDelete="CASCADE")
     * @Exclude
     * @var Concept
     */
    protected $conceptAsDefinition;
    /**
     * @ORM\ManyToOne(targetEntity="Concept", inversedBy="alternativeDefinitions")
     * @ORM\JoinColumn(name="concept_alternative_definition_id", referencedColumnName="id", onDelete="CASCADE")
     * @Exclude
     * @var Concept
     */
    protected $conceptAsAlternativeDefinition;
    /**
     * @ORM\ManyToOne(targetEntity="Concept", inversedBy="relatedConcepts")
     * @ORM\JoinColumn(name="concept_related_concept_id", referencedColumnName="id", onDelete="CASCADE")
     * @Exclude
     * @var Concept
     */
    protected $conceptAsRelatedConcept;
    /**
     * @ORM\ManyToOne(targetEntity="Concept", inversedBy="media")
     * @ORM\JoinColumn(name="concept_media_id", referencedColumnName="id", onDelete="CASCADE")
     * @Exclude
     * @var Concept
     */
    protected $conceptAsMedia;

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

    public function getFormat_type() {
        return $this->format_type;
    }

    public function setFormat_type($format_type) {
        $this->format_type = $format_type;
    }

    public function getText_formatted() {
        return $this->text_formatted;
    }

    public function setText_formatted($text_formatted) {
        $this->text_formatted = $text_formatted;
    }

    public function getTextFormatted() {
        return $this->getText_formatted();
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

    public function getConceptAsMedia() {
        return $this->conceptAsMedia;
    }

    public function setConceptAsMedia(Concept $conceptAsMedia) {
        $this->conceptAsMedia = $conceptAsMedia;
    }

    public function getConcept() {
        if(isset($this->conceptAsAlternativeDefinition)) {
            return $this->conceptAsAlternativeDefinition;
        } else if(isset($this->conceptAsDefinition)) {
            return $this->conceptAsDefinition;
        } else if(isset($this->conceptAsName)) {
            return $this->conceptAsName;
        } else if(isset($this->conceptAsRelatedConcept)) {
            return $this->conceptAsRelatedConcept;
        } else if(isset($this->conceptAsMedia)) {
            return $this->conceptAsMedia;
        } else {
            throw new \Exception('Could not find concept');
        }
    }
}
?>