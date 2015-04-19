<?php
namespace Vispanlab\AdminBundle\Voter;

use Sonata\AdminBundle\Admin\Admin;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Acl\Voter\AclVoter;

class AdminAclVoter extends AclVoter
{
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if(($user = $token->getUser()) instanceof UserInterface) {
            if($user->hasRole('ROLE_ADMIN')) {
                return self::ACCESS_GRANTED;
            }
        }
        return self::ACCESS_ABSTAIN;
    }
}