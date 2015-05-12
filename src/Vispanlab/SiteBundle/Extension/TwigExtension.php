<?php

namespace Vispanlab\SiteBundle\Extension;

class TwigExtension extends \Twig_Extension
{
  protected $container;

  public function __construct($container) {
      $this->container = $container;
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters()
  {
    return array(
        'links_convert_filter' => new \Twig_Filter_Method($this, 'linksConvertFilter'),
    );
  }

  function linksConvertFilter($content, $separator, $locale, $routeName, $routeIdName)
  {
      $strippedContent = strip_tags($content);
      $links = array_filter(array_map('trim', explode($separator, $strippedContent)));
      foreach($links as &$curLink) {
          $query = $this->container->get('doctrine')->getManager()->createQuery('SELECT c,
              (CASE WHEN d.text_formatted like :curLinkEnd THEN 0
               WHEN d.text_formatted like :curLinkExtra THEN 1
               WHEN d.text_formatted like :curLinkFront THEN 2
               ELSE 3
              END) HIDDEN relevance
              FROM Vispanlab\SiteBundle\Entity\Concept c
              JOIN c.name d
              WHERE d.text_formatted LIKE :curLink AND d.locale = :locale
              ORDER BY relevance, d.text_formatted');
          $query->setParameter('curLink', '%'.$curLink.'%');
          $query->setParameter('curLinkEnd', $curLink.'%');
          $query->setParameter('curLinkExtra', '% %'.$curLink.'% %');
          $query->setParameter('curLinkFront', '%'.$curLink);
          $query->setParameter('locale', $locale);
          foreach($query->getResult() as $curResult) {
              $curLink = '<a href="'.$this->container->get('router')->generate($routeName, array($routeIdName => $curResult->getId())).'">'.$curLink.'</a>';
              break;
          }
      }
      return implode(' '.$separator.' ', $links);
  }

  /**
   * Returns the name of the extension.
   *
   * @return string The extension name
   */
  public function getName()
  {
    return 'twig_site_extension';
  }
}