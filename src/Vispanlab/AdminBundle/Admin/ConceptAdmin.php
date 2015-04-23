<?php
namespace Vispanlab\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Vispanlab\SiteBundle\Form\Type\LangTextType;

class ConceptAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'id' // name of the ordered field (default = the model id
    );
    protected $parentAssociationMapping = 'areaofexpertise';

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
            ->add('areaofexpertise', null, array('required' => true))
            ->add('name', 'sonata_type_collection', array(), array('edit' => 'inline', 'inline' => 'table'))
            ->add('definition', 'sonata_type_collection', array(), array('edit' => 'inline', 'inline' => 'table'))
            ->add('alternativeDefinitions', 'sonata_type_collection', array(), array('edit' => 'inline', 'inline' => 'table'))
            ->add('relatedConcepts', 'sonata_type_collection', array(), array('edit' => 'inline', 'inline' => 'table'))
            //->add('media', 'sonata_type_native_collection', array('allow_add' => true, 'type' => 'entity', 'options' => array('class' => 'Application\Sonata\MediaBundle\Entity\Media'), 'allow_delete' => true, ))
            ->add('media')
            ->add('comments', null, array('help' => 'comments_placeholder'))
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
            ->add('areaofexpertise')
            ->add('name', 'langtext')
            ->add('definition', 'langtext')
            ->add('alternativeDefinitions', 'langtext')
            ->add('relatedConcepts', 'langtext')
            ->add('media', 'media')
            ->add('comments')
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
            ->add('areaofexpertise')
            //->add('name', 'doctrine_orm_model_autocomplete', array(), null, array('property'=>'text',))
        ;
        parent::configureDatagridFilters($datagridMapper);
    }

    // Use uploadable manager to upload the file
    public function prePersist($concept)
    {
        foreach($concept->getName() as $definition) {
            $definition->setConceptAsName($concept);
        }
        foreach($concept->getDefinition() as $definition) {
            $definition->setConceptAsDefinition($concept);
        }
        foreach($concept->getAlternativeDefinitions() as $definition) {
            $definition->setConceptAsAlternativeDefinition($concept);
        }
        foreach($concept->getRelatedConcepts() as $definition) {
            $definition->setConceptAsRelatedConcept($concept);
        }
    }

    public function preUpdate($recipe) {
        return $this->prePersist($recipe);
    }
}