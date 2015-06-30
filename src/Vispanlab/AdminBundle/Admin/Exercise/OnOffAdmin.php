<?php
namespace Vispanlab\AdminBundle\Admin\Exercise;

use Vispanlab\SiteBundle\Entity\Exercise\BaseExercise;
use Vispanlab\SiteBundle\Form\Type\MultipleChoiceAnswerType;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class OnOffAdmin extends MultipleChoiceAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('subjectarea', null, array('required' => true, 'group_by' => 'areaofexpertise',))
            ->add('question')
            ->add('correctAnswer', 'integer', array('help' => 'on_off_correct_answer_help'))
            ->add('showInEvaluationTest', 'choice', array('choices' => BaseExercise::getShowInEvaluationTestChoices()))
        ;
    }
}