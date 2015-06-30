<?php
namespace Vispanlab\SiteBundle\Entity;

use Vispanlab\SiteBundle\Entity\Exercise\BaseExercise;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 */
class SubjectArea {
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column (name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="AreaOfExpertise", inversedBy="subjectAreas")
     * @ORM\JoinColumn(name="areaofexpertise_id", referencedColumnName="id", onDelete="CASCADE")
     * @var AreaOfExpertise
     */
    protected $areaofexpertise;
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
     * @ORM\OneToMany(targetEntity="Vispanlab\SiteBundle\Entity\Exercise\BaseExercise", mappedBy="subjectarea")
     */
    protected $exercises;

    public function __construct() {
        $this->exercises = new ArrayCollection();
    }

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

    public function getExercises() {
        return $this->exercises;
    }

    public function setExercises($exercises) {
        $this->exercises = $exercises;
    }

    public function getExercisesByClass($class) {
        return $this->exercises->filter(function($e) use ($class) {
            $classname = basename(str_replace('\\', '/', get_class($e)));
            return $classname == $class;
        });
    }

    public function getExercisesForEvaluationTest() {
        return $this->exercises->filter(function($e) {
            return (method_exists($e, 'getShowInEvaluationTest') && ($e->getShowInEvaluationTest() == BaseExercise::EVALUATION_TEST_EXERCISE || $e->getShowInEvaluationTest() == BaseExercise::BOTH_EXERCISE));
        });
    }

    function stripGrAccent($tempName)
    {
      $utf8_str_split = function($str='',$len=1){
          preg_match_all("/./u", $str, $arr);
          $arr = array_chunk($arr[0], $len);
          $arr = array_map('implode', $arr);
          return $arr;
      };
      $tempName = str_replace($utf8_str_split("ΆάΈέΉήΌόΎύΏώί"), $utf8_str_split("ααεεηηοουυωωι"), $tempName);
      return str_replace($utf8_str_split("αβγδεζηθικλμνξοπρστυφχψως"), $utf8_str_split("ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩΣ"), $tempName);
    }

    public function __toString() {
        if(!isset($this->nameEl)) {
            return 'Νέα Θεματική Ενότητα';
        } else {
            return str_replace('<BR />', ' ', $this->nameEl);
        }
    }
}
?>