<?php
namespace Vispanlab\AdminBundle\Admin\Exercise;

use Vispanlab\SiteBundle\Form\Type\MultipleChoiceAnswerType;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class MultipleChoiceAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'id' // name of the ordered field (default = the model id
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('acl');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('subjectarea', null, array('required' => true))
            ->add('question')
            ->add('answers', 'collection', array(
                'type' => new MultipleChoiceAnswerType(),
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('correctAnswer', 'integer', array('help' => 'correct_answer_help'))
        ;
        parent::configureFormFields($formMapper);
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
            )))
            ->addIdentifier('id')
            ->add('subjectarea')
            ->add('question')
            ->add('answers', 'answers_array')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('subjectarea')
        ;
        parent::configureDatagridFilters($datagridMapper);
    }
}