<?php
namespace Vispanlab\AdminBundle\Admin\Exercise;

use Vispanlab\SiteBundle\Form\Type\MultipleChoiceAnswerType;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class SolvedAdmin extends Admin
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
            ->add('subjectarea', null, array('required' => true, 'group_by' => 'areaofexpertise',))
            ->add('goal')
            ->add('question')
            ->add('data')
            ->add('requested')
            ->add('methodology')
            ->add('algorithm')
            ->add('solution')
            ->add('result')
            ->add('media', 'sonata_formatter_type', array(
                'source_field'         => 'media',
                'source_field_options' => array('attr' => array('class' => 'span10', 'rows' => 20)),
                'format_field'         => 'format_type',
                'target_field'         => 'media_formatted',
                'ckeditor_context'     => 'default',
                'event_dispatcher'     => $formMapper->getFormBuilder()->getEventDispatcher(),
                'listener'             => true,
            ))
            ->add('relatedConcepts')
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
            ->add('goal')
            ->add('question')
            ->add('data')
            ->add('requested')
            ->add('methodology')
            ->add('algorithm')
            ->add('solution')
            ->add('result')
            ->add('relatedConcepts')
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

    private $securityContext = null;

    public function setSecurityContext($securityContext) {
        $this->securityContext = $securityContext;
    }

    public function createQuery($context = 'list')
    {
        $proxyQuery = parent::createQuery($context);
        $user = $this->securityContext->getToken()->getUser();
        if(!$user->hasRole('ROLE_ADMIN') && $user->hasRole('ROLE_AREA_ADMIN')) {
            $proxyQuery->join($proxyQuery->getRootAlias().'.subjectarea', 'sa');
            $proxyQuery->join('sa.areaofexpertise', 'aoe');
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
}