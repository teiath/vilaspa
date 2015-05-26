<?php
namespace Vispanlab\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class UserAdmin extends Admin
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
            ->add('name')
            ->add('surname')
            ->add('roles', 'choice', array(
                   'choices'  => array_merge(array('ROLE_USER' => 'ROLE_USER', 'ROLE_STUDENT' => 'ROLE_STUDENT', 'ROLE_CIVILIAN' => 'ROLE_CIVILIAN', 'ROLE_ADMIN' => 'ROLE_ADMIN'), $this->getDynamicRoles()),
                   'multiple' => true,
                )
            );
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
            ->add('username')
            ->add('name')
            ->add('surname')
            ->add('roles', 'user_roles')
            ->add('lastLogin')
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
            ->add('username')
        ;
        parent::configureDatagridFilters($datagridMapper);
    }

    private function getDynamicRoles() {
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $aoe = $em->getRepository('Vispanlab\SiteBundle\Entity\AreaOfExpertise')->findAll();
        $roles = array('ROLE_ADMIN' => 'ROLE_ADMIN');
        foreach($aoe as $curAoe) {
            $convertedAoe = 'ROLE_AREA_ADMIN_'.strtoupper($curAoe->getUrl());
            $roles[$convertedAoe] = $convertedAoe;
        }
        return $roles;
    }
}