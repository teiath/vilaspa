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
            if($user->hasRole('ROLE_AREA_ADMIN')) {
                foreach ($attributes as $attribute) {
                    if($attribute == 'LIST' || $attribute == 'VIEW' || $attribute == 'EXPORT' || strtolower($attribute) == 'edit' || strtolower($attribute) == 'create' || strtolower($attribute) == 'delete') {
                        if(strpos(get_class($object), 'Concept') !== false || strpos(get_class($object), 'Definition') !== false || strpos(get_class($object), 'Media') !== false) {
                            return self::ACCESS_GRANTED;
                        }
                    }
                }
            }
        }
        return self::ACCESS_ABSTAIN;
    }
}