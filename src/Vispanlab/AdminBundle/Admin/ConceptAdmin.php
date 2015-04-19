<?php
namespace Vispanlab\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ConceptAdmin extends Admin
{
    protected $uploadableManager;

    protected $datagridValues = array(
        '_sort_order' => 'DESC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'id' // name of the ordered field (default = the model id
    );
    protected $parentAssociationMapping = 'areaofexpertise';

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('areaofexpertise', null, array('required' => true))
            ->add('nameEl')
            ->add('nameEn')
            ->add('definitionEl')
            ->add('definitionEn')
            ->add('alternativeDefintions', 'collection', array(
                'type'   => 'text',
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'options'  => array(
                    'required'  => false,
                    'label' => 'Ορισμός'
                )
            ))
            ->add('relatedConcepts', 'collection', array(
                'type'   => 'text',
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'options'  => array(
                    'required'  => false,
                    'label' => 'Ορισμός'
                )
            ))
            ->add('newImageExcerpt', 'file', array('required' => false))
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
            ->add('nameEl')
            ->add('nameEn')
            ->add('definitionEl')
            ->add('definitionEn')
            ->add('alternativeDefintions')
            ->add('relatedConcepts')
            ->add('imageExcerpt.imagePath', 'image')
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
            ->add('nameEl')
            ->add('nameEn')
            ->add('definitionEl')
            ->add('definitionEn')
        ;
        parent::configureDatagridFilters($datagridMapper);
    }

    public function setUploadableManager($uploadableManager) {
        $this->uploadableManager = $uploadableManager;
    }

    // Use uploadable manager to upload the file
    public function prePersist($concept)
    {
        if($concept->getNewImageExcerpt() != null) {
            $concept->getImageExcerpt()->setPhoto($concept->getNewImageExcerpt());
            $em = $this->modelManager->getEntityManager(get_class($concept->getImageExcerpt()));
            $em->persist($concept->getImageExcerpt());
            $this->uploadableManager->markEntityToUpload($concept->getImageExcerpt(), $concept->getImageExcerpt()->getPhoto());
        }
    }

    public function preUpdate($recipe) {
        return $this->prePersist($recipe);
    }
}