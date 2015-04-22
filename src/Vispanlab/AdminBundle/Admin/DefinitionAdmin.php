<?php
namespace Vispanlab\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class DefinitionAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'id' // name of the ordered field (default = the model id
    );
    protected $parentAssociationMapping = 'definition';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('locale', 'language', array('preferred_choices' => array('el', 'en', 'fr', 'de')))
            ->add('text')
        ;
        parent::configureFormFields($formMapper);
    }
}