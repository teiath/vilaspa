<?php
namespace Vilaspa\UserBundle\Form\Type;

use Vilaspa\SiteBundle\Form\Type\DogType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    public function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array('required' => true))
            ->add('email', 'email', array('required' => true))
            ->add('address', 'text', array('required' => true))
            ->add('latitude', 'hidden', array('required' => true,))
            ->add('longitude', 'hidden', array('required' => true,))
        ;
    }

    public function getName()
    {
        return 'vilaspa_user_profile';
    }
}
