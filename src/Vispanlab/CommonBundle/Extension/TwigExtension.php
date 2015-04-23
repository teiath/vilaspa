<?php

namespace Vispanlab\CommonBundle\Extension;

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
        'url_decode' => new \Twig_Filter_Method($this, 'urlDecode'),
        'roman_numeral' => new \Twig_Filter_Method($this, 'romanNumeral'),
        'md5' => new \Twig_Filter_Method($this, 'md5'),
    );
  }

  public function getTests()
  {
    return [
        'instanceof' =>  new \Twig_Function_Method($this, 'isInstanceof')
    ];
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
      return array(
        'base_template' => $this->getBaseTemplate(),
        'admin_pool'      => $this->container->get('sonata.admin.pool'),
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

  public function md5($string) {
      return md5($string);
  }

  function romanNumeral($integer, $upcase = true)
  {
    $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1);
    $return = '';
    while($integer > 0)
    {
        foreach($table as $rom=>$arb)
        {
            if($integer >= $arb)
            {
                $integer -= $arb;
                $return .= $rom;
                break;
            }
        }
    }

    return $return;
  }

  /**
   * @param $var
   * @param $instance
   * @return bool
   */
  public function isInstanceof($var, $instance) {
    return  $var instanceof $instance;
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