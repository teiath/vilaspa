<?php

namespace Vispanlab\UserBundle\Security\Constraints;

use Vispanlab\UserBundle\Security\TEIUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEntityInTEIValidator extends ConstraintValidator
{
    /**
     * @var TEIUserProvider
     */
    private $userProvider;

    public function __construct(TEIUserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param object     $entity
     * @param Constraint $constraint
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate($entity, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueEntityInTEI) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\UniqueEntityInTEI');
        }

        if (!is_array($constraint->fields) && !is_string($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        if (null !== $constraint->errorPath && !is_string($constraint->errorPath)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
        }

        $fields = (array) $constraint->fields;

        if (0 === count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        /*try {
            $password = $entity->getPlainPassword();
            foreach($fields as $curField) {
                $method = 'get'.ucfirst($curField);
                $this->userProvider->loadUserByUsernameAndPassword($entity->$method(), $password);
            }
        } catch(UsernameNotFoundException $e) {
            return;
        }*/
        foreach($fields as $curField) {
            $method = 'get'.ucfirst($curField);
            if(!$this->userProvider->userExistsInTEI($entity->$method())) { return; }
        }

        $errorPath = null !== $constraint->errorPath ? $constraint->errorPath : $fields[0];

        $this->buildViolation($constraint->message)
            ->atPath($errorPath)
            ->setInvalidValue($errorPath)
            ->addViolation();
    }
}
