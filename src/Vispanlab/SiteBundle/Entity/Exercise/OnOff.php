<?php
namespace Vispanlab\SiteBundle\Entity\Exercise;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @ORM\Entity
 */
class OnOff extends MultipleChoice {
    public function __construct() {
        $this->answers = array(
            array('answer' => 've_on'),
            array('answer' => 've_off'),
        );
    }
}
?>