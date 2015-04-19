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
    protected $nameEl;
    /**
     * @ORM\Column (name="name_en", type="string", nullable=true)
     */
    protected $nameEn;
    /**
     * @ORM\Column (name="defintion", type="string")
     */
    protected $definitionEl;
    /**
     * @ORM\Column (name="defintion_en", type="string", nullable=true)
     */
    protected $definitionEn;
    /**
     * @ORM\Column (name="alternative_definitions", type="array")
     */
    protected $alternativeDefintions;
    /**
     * @ORM\Column (name="related_concepts", type="array")
     */
    protected $relatedConcepts;
    /**
     * @ORM\ManyToOne(targetEntity="Vispanlab\CommonBundle\Entity\ImageFile")
     * @ORM\JoinColumn(name="image_excerpt_id", referencedColumnName="id", onDelete="SET NULL")
     * @var ImageFile
     */
    protected $imageExcerpt;
    /**
     * @Assert\File(
     *     mimeTypes={"image/jpeg", "image/pjpeg", "image/png", "image/x-png"}
     * )
     */
    protected $newImageExcerpt;
    /**
     * @ORM\Column (name="comments", type="text", nullable=true)
     */
    protected $comments;

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

    public function getDefinitionEl() {
        return $this->definitionEl;
    }

    public function setDefinitionEl($definitionEl) {
        $this->definitionEl = $definitionEl;
    }

    public function getDefinitionEn() {
        return $this->definitionEn;
    }

    public function setDefinitionEn($definitionEn) {
        $this->definitionEn = $definitionEn;
    }

    public function getAlternativeDefintions() {
        return $this->alternativeDefintions;
    }

    public function setAlternativeDefintions($alternativeDefintions) {
        $this->alternativeDefintions = $alternativeDefintions;
    }

    public function getRelatedConcepts() {
        return $this->relatedConcepts;
    }

    public function setRelatedConcepts($relatedConcepts) {
        $this->relatedConcepts = $relatedConcepts;
    }

    public function getImageExcerpt() {
        if(!isset($this->imageExcerpt)) { $this->imageExcerpt = new ImageFile(); }
        return $this->imageExcerpt;
    }

    public function setImageExcerpt(ImageFile $imageExcerpt) {
        $this->imageExcerpt = $imageExcerpt;
    }

    public function getNewImageExcerpt() {
        return $this->newImageExcerpt;
    }

    public function setNewImageExcerpt($newImageExcerpt) {
        $this->newImageExcerpt = $newImageExcerpt;
    }

    public function getComments() {
        return $this->comments;
    }

    public function setComments($comments) {
        $this->comments = $comments;
    }

    public function __toString() {
        if(!isset($this->nameEl)) {
            return 'Νέα Έννοια';
        } else {
            return $this->nameEl;
        }
    }
}
?>