<?php

namespace Vilaspa\CommonBundle\Extension;

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
        'url_decode' => new \Twig_Filter_Method($this, 'urlDecode')
    );
  }

  /**
   * @return string
   */
  private function getBaseTemplate() {
      if ($this->container->hasScope('request') && $this->container->get('request')->isXmlHttpRequest()) {
          return $this->container->get('sonata.admin.pool')->getTemplate('ajax');
      }
      return $this->container->get('sonata.admin.pool')->getTemplate('layout');
  }

  public function getGlobals() {
      if (false === $this->container->get('security.context')->isGranted('ROLE_USER')) {
          return array();
      }
      $areasofexpertise = $this->container->get('doctrine')->getRepository('Vilaspa\SiteBundle\Entity\AreaOfExpertise')->findAll();
      return array(
        'base_template' => $this->getBaseTemplate(),
        'admin_pool'      => $this->container->get('sonata.admin.pool'),
        'all_areas_of_expertise' => $areasofexpertise,
      );
  }

  /**
   * URL Decode a string
   *
   * @param string $url
   *
   * @return string The decoded URL
   */
  public function urlDecode( $url )
  {
    return urldecode( $url );
  }

  /**
   * Returns the name of the extension.
   *
   * @return string The extension name
   */
  public function getName()
  {
    return 'twig_extension';
  }
}