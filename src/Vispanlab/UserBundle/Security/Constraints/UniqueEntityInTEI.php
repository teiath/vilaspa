<?php

namespace Vispanlab\UserBundle\Security\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueEntityInTEI extends Constraint
{
    public $message = 'This value is already used in TEI.';
    public $fields = array();
    public $errorPath = null;
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return array('fields');
    }

    /**
     * The validator must be defined as a service with this name.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'tei_unique';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption()
    {
        return 'fields';
    }
}
