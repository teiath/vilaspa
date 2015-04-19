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

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, EntityManager $entityManager)
    {
        $this->factory = $factory;
        $this->em = $entityManager;
    }

    public function createSideMenu(Request $request)
    {
        $areasofexpertise = $this->em->getRepository('Vispanlab\SiteBundle\Entity\AreaOfExpertise')->findAll();
        $menu = $this->factory->createItem('root');

        foreach($areasofexpertise as $curArea) {
            $areaMenu = $menu->addChild($curArea->getName(), array('uri' => '#', 'attributes' => array('class' => 'home')));
            $areaMenu->addChild('Βιβλιοθήκη Εννοιών', array('route' => 'concept_library', 'routeParameters' => array('aoe' => $curArea->getUrl()), 'attributes' => array('class' => 'home')));
            $areaMenu->addChild('Εικονικές Ασκήσεις', array('route' => 'virtual_exercises', 'routeParameters' => array('aoe' => $curArea->getUrl()), 'attributes' => array('class' => 'home')));
        }
        $menu->addChild('Οδηγίες Χρήσης', array('uri' => '#docs', 'attributes' => array('class' => 'home')));

        return $menu;
    }
}