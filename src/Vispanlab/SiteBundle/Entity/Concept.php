<?php
namespace Vispanlab\SiteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Exclude;

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
     * @ORM\ManyToOne(targetEntity="AreaOfExpertise")
     * @ORM\JoinColumn(name="areaofexpertise_id", referencedColumnName="id", onDelete="CASCADE")
     * @var AreaOfExpertise
     * @Exclude
     */
    protected $areaofexpertise; // DEPRECTED - TO REMOVE
    /**
     * @ORM\ManyToMany(targetEntity="AreaOfExpertise", inversedBy="concepts")
     * @ORM\JoinTable(name="concepts_areas",
     *      joinColumns={@ORM\JoinColumn(name="concept_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="aoe_id", referencedColumnName="id")}
     *      )
     * @Exclude
     */
    protected $areasofexpertise;
    /**
     * @ORM\OneToMany(targetEntity="Definition", mappedBy="conceptAsName", cascade={"persist"}, orphanRemoval=true, fetch="EAGER")
     */
    protected $name;
    /**
     * @ORM\OneToMany(targetEntity="Definition", mappedBy="conceptAsDefinition", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"locale" = "ASC"})
     */
    protected $definition;
    /**
     * @ORM\OneToMany(targetEntity="Definition", mappedBy="conceptAsAlternativeDefinition", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"locale" = "ASC"})
     */
    protected $alternativeDefinitions;
    /**
     * @ORM\OneToMany(targetEntity="Definition", mappedBy="conceptAsRelatedConcept", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"locale" = "ASC"})
     */
    protected $relatedConcepts;
    /**
     * @ORM\OneToMany(targetEntity="Definition", mappedBy="conceptAsMedia", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"locale" = "ASC"})
     */
    protected $media;

    protected $commentsFormatType = 'richhtml';
    /**
     * @ORM\Column (name="comments", type="text", nullable=true)
     */
    protected $comments;

    protected $commentsFormatted;

    public function __construct() {
        $this->areasofexpertise = new ArrayCollection();
        $this->name = new ArrayCollection();
        $this->definition = new ArrayCollection();
        $this->alternativeDefinitions = new ArrayCollection();
        $this->relatedConcepts = new ArrayCollection();
        $this->media = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getAreasofexpertise() {
        return $this->areasofexpertise;
    }

    public function setAreasofexpertise($areasofexpertise) {
        $this->areasofexpertise = $areasofexpertise;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function addName(Definition $name) {
        $this->getName()->add($name);
    }

    public function hasNameForLang($lang) {
        return $this->hasFieldForLang('getName', $lang);
    }

    private function getFieldForLang($field, $lang) {
        if($this->$field() == null) { return null; }
        foreach($this->$field() as $curName) {
            if($curName->getLocale() == $lang) {
                return $curName;
            }
        }
        throw new \Exception($field.' NOT FOUND FOR LANG '.$lang);
    }

    private function hasFieldForLang($field, $lang) {
        if($this->$field() == null) { return null; }
        foreach($this->$field() as $curName) {
            if($curName->getLocale() == $lang) {
                return true;
            }
        }
        return false;
    }

    /*private function getArrayFieldForLang($field, $lang) {
        if($this->$field() == null) { return null; }
        $result = array();
        foreach($this->$field() as $curName) {
            if($curName->getLocale() == $lang) {
                $result[] = $curName;
            }
        }
        return $result;
    }*/

    private function getArrayFieldForOtherLang($field, $lang) {
        $definitions = array();
        foreach($this->$field() as $curDefinition) {
            if($curDefinition->getLocale() != $lang) {
                $definitions[] = $curDefinition;
            }
        }
        return $definitions;
    }

    public function getNameForLang($lang) {
        return $this->getFieldForLang('getName', $lang);
    }

    public function getNameLangs() {
        $langs = array();
        foreach($this->name as $curName) {
            $langs[] = $curName->getLocale();
        }
        return $langs;
    }

    public function getNameForOtherLang($lang) {
        return $this->getArrayFieldForOtherLang('getName', $lang);
    }

    public function getDefinition() {
        return $this->definition;
    }

    public function setDefinition($definition) {
        $this->definition = $definition;
    }

    public function addDefinition(Definition $definition) {
        $this->getDefinition()->add($definition);
    }

    public function getDefinitionForLang($lang) {
        return $this->getFieldForLang('getDefinition', $lang);
    }

    public function hasDefinitionForLang($lang) {
        return $this->hasFieldForLang('getDefinition', $lang);
    }

    public function getDefinitionForOtherLang($lang) {
        return $this->getArrayFieldForOtherLang('getDefinition', $lang);
    }

    public function getAlternativeDefinitions() {
        return $this->alternativeDefinitions;
    }

    public function setAlternativeDefinitions($alternativeDefinitions) {
        $this->alternativeDefinitions = $alternativeDefinitions;
    }

    public function addAlternativeDefinitions(Definition $definition) {
        $this->getAlternativeDefinitions()->add($definition);
    }

    public function getRelatedConcepts() {
        return $this->relatedConcepts;
    }

    public function setRelatedConcepts($relatedConcepts) {
        $this->relatedConcepts = $relatedConcepts;
    }

    public function addRelatedConcepts(Definition $definition) {
        $this->getRelatedConcepts()->add($definition);
    }

    public function getMedia() {
        return $this->media;
    }

    public function setMedia($media) {
        $this->media = $media;
    }

    public function addMedia(Definition $media) {
        $this->getMedia()->add($media);
    }

    public function removeMedia($media) {
        $this->getMedia()->removeElement($media);
    }

    function getCommentsFormatType() {
        return $this->commentsFormatType;
    }

    function setCommentsFormatType($commentsFormatType) {
        $this->commentsFormatType = $commentsFormatType;
    }

    public function getComments() {
        return $this->comments;
    }

    public function setComments($comments) {
        $this->comments = $comments;
    }

    function getCommentsFormatted() {
        return $this->comments;
    }

    function setCommentsFormatted($commentsFormatted) {
        $this->comments = $commentsFormatted;
    }

    public function getCommentsAsDefinition() {
        $def = new Definition();
        $def->setLocale('el');
        $def->setText_formatted($this->getComments());
        $def->setConceptAsName($this);
        return $def;
    }

    public function getNameCondensed() {
        return strip_tags(implode('<br style="mso-data-placement:same-cell;" />', $this->getName()->map(function($d) { return $d->getLocale().':'.$d->getTextFormatted(); })->toArray()), '<br>');
    }

    public function getDefinitionCondensed() {
        return strip_tags(implode('<br style="mso-data-placement:same-cell;" />', $this->getDefinition()->map(function($d) { return $d->getLocale().':'.$d->getTextFormatted(); })->toArray()), '<br>');
    }

    public function getAlternativeDefinitionsCondensed() {
        return strip_tags(implode('<br style="mso-data-placement:same-cell;" />', $this->getAlternativeDefinitions()->map(function($d) { return $d->getLocale().':'.$d->getTextFormatted(); })->toArray()), '<br>');
    }

    public function getRelatedConceptsCondensed() {
        return strip_tags(implode('<br style="mso-data-placement:same-cell;" />', $this->getRelatedConcepts()->map(function($d) { return $d->getLocale().':'.$d->getTextFormatted(); })->toArray()), '<br>');
    }

    public function getMediaCondensed() {
        return implode('<br style="mso-data-placement:same-cell;" />', $this->getMedia()->map(function($d) { return $d->getLocale().':'.$d->getTextFormatted(); })->toArray());
    }

    public function __toString() {
        if($this->getName()->count() <= 0 || $this->getNameForLang('el') == null) {
            return 'Νέα Έννοια';
        } else {
            return strip_tags($this->getNameForLang('el')->getTextFormatted());
        }
    }
}
?>