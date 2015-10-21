<?php
namespace Vispanlab\UserBundle\Entity;

use Vispanlab\SiteBundle\Entity\AreaOfExpertise;
use Vispanlab\SiteBundle\Entity\Exercise\BaseExercise;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation\Exclude;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 */
class UserScore {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Vispanlab\SiteBundle\Entity\AreaOfExpertise")
     * @ORM\JoinColumn(name="areaofexpertise_id", referencedColumnName="id", onDelete="CASCADE")
     * @Exclude
     * @var Vispanlab\SiteBundle\Entity\AreaOfExpertise
     */
    protected $areaofexpertise;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userScore")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * @Exclude
     * @var User
     */
    protected $user;
    /**
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    protected $score;

    public function getAreaofexpertise() {
        return $this->areaofexpertise;
    }

    public function setAreaofexpertise(AreaOfExpertise $areaofexpertise) {
        $this->areaofexpertise = $areaofexpertise;
    }

    function getUser() {
        return $this->user;
    }

    function getScore() {
        return $this->score;
    }

    function setUser(User $user) {
        $this->user = $user;
    }

    function setScore($score) {
        $this->score = $score;
    }
}
?>