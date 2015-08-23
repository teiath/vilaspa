<?php
namespace Vispanlab\SiteBundle\Menu;

use Knp\Menu\FactoryInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class SidebarMenuBuilder
{
    private $factory;
    /**
     * @var EntityManager
     */
    private $em;

    private $securityContext;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, EntityManager $entityManager, $securityContext)
    {
        $this->factory = $factory;
        $this->em = $entityManager;
        $this->securityContext = $securityContext;
    }

    public function createSideMenu(Request $request)
    {
        $areasofexpertise = $this->em->getRepository('Vispanlab\SiteBundle\Entity\AreaOfExpertise')->findBy(array(), array('sortOrder' => 'ASC', 'id' => 'ASC'));
        $menu = $this->factory->createItem('root');

        foreach($areasofexpertise as $curArea) {
            $areaMenu = $menu->addChild($curArea->getName($request->getLocale()), array('uri' => '#', 'attributes' => array('class' => 'home', 'under_construction' => $curArea->getUnderConstruction())));
            $libraryMenu = $areaMenu->addChild('common.concept_library', array('route' => 'concept_library', 'routeParameters' => array('aoe' => $curArea->getUrl()), 'attributes' => array('class' => 'home')));
            foreach($curArea->getConcepts($request->getLocale()) as $curConcept) {
                $libraryMenu->addChild($curConcept->getNameForLang($request->getLocale())->getText_formatted(), array('route' => 'concept', 'routeParameters' => array('aoe' => $curArea->getUrl(), 'concept' => $curConcept->getId()), 'attributes' => array('class' => 'home')));
            }
            $veMenu = $areaMenu->addChild('common.virtual_assignments', array('route' => 'virtual_exercises', 'routeParameters' => array('aoe' => $curArea->getUrl()), 'attributes' => array('class' => 'home')));
            $exerciseTypes = array('MultipleChoice', 'OnOff', 'Solved', 'Matching', 'Unsolved', 'EvaluationTest');
            foreach($exerciseTypes as $curExerciseType) {
                $veMenu->addChild($curExerciseType.'null_subject_area', array('label' => 'virtual_exercises.'.$curExerciseType, 'route' => 'show_exercises', 'routeParameters' => array('aoe' => $curArea->getUrl(), 'type' => $curExerciseType), 'attributes' => array('class' => 'home')));
            }
            foreach($curArea->getSubjectAreas($request->getLocale()) as $curSubjectArea) {
                foreach($exerciseTypes as $curExerciseType) {
                    $veMenu->addChild($curExerciseType.$curSubjectArea->getUrl(), array('label' => 'virtual_exercises.'.$curExerciseType, 'route' => 'show_exercises', 'routeParameters' => array('aoe' => $curArea->getUrl(), 'sa' => $curSubjectArea->getUrl(), 'type' => $curExerciseType), 'attributes' => array('class' => 'home')));
                }
            }
        }

        $menu->addChild('common.user_guide', array('route' => 'user_guide', 'attributes' => array('class' => 'with-spacing home')));
        if($this->securityContext->isGranted('ROLE_ADMIN') || $this->securityContext->isGranted('ROLE_AREA_ADMIN')) {
            if(strpos($request->getRequestUri(), 'admin') === false) {
                $menu->addChild('common.admin_env_link', array('route' => 'sonata_admin_dashboard', 'attributes' => array('class' => 'home')));
            } else {
                $menu->addChild('common.user_env_link', array('route' => 'home', 'attributes' => array('class' => 'home')));
            }
        }

        return $menu;
    }

    public function createBottomMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild('common.admin_guide', array('uri' => '/admin_guide.docx', 'attributes' => array('class' => 'home')));
        $menu->addChild('common.analytics', array('uri' => 'https://www.google.com/analytics/web/?hl=en#report/visitors-overview/a66591538w103050861p107149363/', 'attributes' => array('class' => 'home')));
        $menu->addChild('common.tech_guide', array('uri' => '/tech_guide.docx', 'attributes' => array('class' => 'home')));
        if($this->securityContext->isGranted('ROLE_ADMIN') || $this->securityContext->isGranted('ROLE_AREA_ADMIN')) {
            if(strpos($request->getRequestUri(), 'admin') === false) {
                $menu->addChild('common.admin_env_link', array('route' => 'sonata_admin_dashboard', 'attributes' => array('class' => 'home')));
            } else {
                $menu->addChild('common.user_env_link', array('route' => 'home', 'attributes' => array('class' => 'home')));
            }
        }
        //$menu->addChild('common.user_guide', array('uri' => '#docs', 'attributes' => array('class' => 'home')));

        return $menu;
    }
}