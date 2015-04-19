<?php
namespace Vispanlab\CommonBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity()
 * @Gedmo\Uploadable(
 *  pathMethod="getRootPath",
 *  filenameGenerator="SHA1",
 *  allowedTypes="image/jpeg,image/pjpeg,image/png,image/x-png"
 * )
 */
class ImageFile
{
    use TimestampableEntity;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(name="path", type="string")
     * @Gedmo\UploadableFilePath
     * @Accessor(getter="getPathObject")
     */
    private $path;
    /**
     * @Assert\File(
     *     mimeTypes={"image/jpeg", "image/pjpeg", "image/png", "image/x-png"}
     * )
     */
    private $photo;

    private $photoUrl;

    /**
     * @ORM\Column(name="mime_type", type="string")
     * @Gedmo\UploadableFileMimeType
     */
    private $mimeType;
    /**
     * @ORM\Column(name="size", type="decimal")
     * @Gedmo\UploadableFileSize
     */
    private $size;

    public function getRootPath() {
        $path = __DIR__.'/../../../../web/upload/images';
        return $path;
    }

    public function getWebPath() {
        return 'upload/images';
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getPathObject() {
        return new Image($this->getImagePath());
    }

    public function getImagePath() {
        if(strpos($this->path, 'no-photo') === false) {
            return $this->getWebPath().'/'.basename($this->getPath());
        } else {
            return $this->path;
        }
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function setPhoto($photo) {
        $this->photo = $photo;
    }

    public function getPhotoUrl() {
        return $this->photoUrl;
    }

    public function setPhotoUrl($photoUrl) {
        $this->photoUrl = $photoUrl;
    }

    public function getMimeType() {
        return $this->mimeType;
    }

    public function setMimeType($mimeType) {
        $this->mimeType = $mimeType;
    }

    public function getSize() {
        return $this->size;
    }

    public function setSize($size) {
        $this->size = $size;
    }
}
