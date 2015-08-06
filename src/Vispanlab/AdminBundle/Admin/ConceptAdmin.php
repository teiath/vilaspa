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
    protected $parentAssociationMapping = 'areasofexpertise';

    private $securityContext = null;

    public function setSecurityContext($securityContext) {
        $this->securityContext = $securityContext;
    }

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
        $user = $this->securityContext->getToken()->getUser();
        if($user->hasRole('ROLE_ADMIN')) {
            $formMapper->add('areasofexpertise', null, array('required' => true));
        }
        $formMapper
            ->add('name', 'sonata_type_collection', array(), array('edit' => 'inline', 'inline' => 'table'))
            ->add('definition', 'sonata_type_collection', array(), array('edit' => 'inline', 'inline' => 'table'))
            ->add('alternativeDefinitions', 'sonata_type_collection', array(), array('edit' => 'inline', 'inline' => 'table'))
            ->add('relatedConcepts', 'sonata_type_collection', array(), array('edit' => 'inline', 'inline' => 'table'))
            //->add('media', 'sonata_type_native_collection', array('allow_add' => true, 'type' => 'entity', 'options' => array('class' => 'Application\Sonata\MediaBundle\Entity\Media'), 'allow_delete' => true, ))
            ->add('media', 'sonata_type_collection', array(), array('edit' => 'inline', 'inline' => 'table'))
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
        $user = $this->securityContext->getToken()->getUser();
        $listMapper
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
            )))
            ->addIdentifier('id');
        if($user->hasRole('ROLE_ADMIN')) {
            $listMapper->add('areasofexpertise');
        }
        $listMapper
            ->add('name', 'langtext')
            ->add('definition', 'langtext')
            ->add('alternativeDefinitions', 'langtext')
            //->add('relatedConcepts', 'langtext')
            //->add('media', 'langtext')
            //->add('comments')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $user = $this->securityContext->getToken()->getUser();
        $datagridMapper->add('id');
        if($user->hasRole('ROLE_ADMIN')) {
            $datagridMapper->add('name.text');
            $datagridMapper->add('areasofexpertise');
        }
        parent::configureDatagridFilters($datagridMapper);
    }

    public function createQuery($context = 'list')
    {
        $proxyQuery = parent::createQuery($context);
        $user = $this->securityContext->getToken()->getUser();
        if(!$user->hasRole('ROLE_ADMIN') && $user->hasRole('ROLE_AREA_ADMIN')) {
            $proxyQuery->join($proxyQuery->getRootAlias().'.areasofexpertise', 'aoe');
            foreach($user->getRoles() as $curRole) {
                if(strpos($curRole, 'ROLE_AREA_ADMIN') === false) { continue; }
                $aoe = substr($curRole, strlen('ROLE_AREA_ADMIN')+1);
                $proxyQuery->andWhere('aoe.url = :aoe'.strtolower($aoe));
                $proxyQuery->setParameter('aoe'.strtolower($aoe), strtolower($aoe));
                break;
            }
        }
        return $proxyQuery;
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
        foreach($concept->getMedia() as $definition) {
            $definition->setConceptAsMedia($concept);
        }
    }

    public function preUpdate($recipe) {
        return $this->prePersist($recipe);
    }

    public function getExportFields()
    {
        return array(
            'id',
            'name' => 'getNameCondensed',
            'definition' => 'getDefinitionCondensed',
            'alternativeDefinitions' => 'getAlternativeDefinitionsCondensed',
            'relatedConcepts' => 'getRelatedConceptsCondensed',
            'media' => 'getMediaCondensed',
            'comments'
        );
    }
}