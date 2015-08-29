<?php

namespace Vispanlab\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MatchingAnswerType extends AbstractType
{
    public static $matchingCount = 0;

    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('answer', null, array('label' => 've_answer '.self::$matchingCount++, 'required' => true,))
            ->add('matches', null, array('label' => 've_matches', 'required' => true, 'sonata_help' => 'Γράψτε την απάντηση του δεξιού μέρους που αντιστοιχεί εδώ. Εαν αντιστοιχεί σε πολλαπλές απαντήσεις χωρίστε με κόμμα (πχ. 1,3,4).'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            //'data_class' => 'Forky\LogisticsBundle\Entity\DeliveryPerson',
            'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return 'matching';
    }
}