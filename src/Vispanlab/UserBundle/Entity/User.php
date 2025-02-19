<?php
namespace Vispanlab\UserBundle\Entity;

use Vispanlab\SiteBundle\Entity\Dog;
use Vispanlab\UserBundle\Wantlet\ORM\Point;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Vispanlab\UserBundle\Security\Constraints\UniqueEntityInTEI;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Accessor;

/**
 * @ORM\Entity
 * @ORM\Table(name="Users")
 * @ORM\Entity(repositoryClass="Vispanlab\UserBundle\Entity\Repositories\UserRepository")
 * @ExclusionPolicy("all")
 * @UniqueEntityInTEI(fields={"username"}, message="User already exists in TEI")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;
    /**
     * @ORM\Column(name="surname", type="string", nullable=true)
     */
    protected $surname;
    /**
     * @ORM\OneToMany(targetEntity="UserScore", mappedBy="user")
     */
    protected $userScore;
    /**
     * @ORM\Column(name="login_source", type="string", nullable=true)
     */
    protected $loginSource = self::LOGIN_SOURCE_LOCAL;
    const LOGIN_SOURCE_LOCAL = 'LOCAL';
    const LOGIN_SOURCE_TEI = 'TEI';

    public function __construct() {
        $this->userScore = new ArrayCollection();
        parent::__construct();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

    function getUserScore() {
        return $this->userScore;
    }

    function setUserScore($userScore) {
        $this->userScore = $userScore;
    }

    public function getUserScoreForAoe($aoe) {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->eq("areaofexpertise", $aoe));
        $score = $this->userScore->matching($criteria);
        if($score->count() > 0) { return $score->first(); } else {
            $score = new UserScore();
            $score->setAreaofexpertise($aoe);
            $score->setUser($this);
            return $score;
        }
    }

    public function getLoginSource() {
        return $this->loginSource;
    }

    public function setLoginSource($loginSource) {
        $this->loginSource = $loginSource;
    }

    public function getRoles() {
        $roles = parent::getRoles();
        foreach($roles as $curRole) {
            if(strpos($curRole, 'ROLE_AREA_ADMIN') !== false) { $roles[] = 'ROLE_AREA_ADMIN'; break; }
        }
        return $roles;
    }
}