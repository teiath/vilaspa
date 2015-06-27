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

  public function getGlobals() {
      return array(
        'langFlags' => array(
            // Languages
            'en' => 'gb',
            'el' => 'gr',
            'uk' => 'gb',
            'da' => 'dk',
            // Countries
            'ad' => 'ad',
            'ae' => 'ae',
            'af' => 'af',
            'ag' => 'ag',
            'ai' => 'ai',
            'al' => 'al',
            'am' => 'am',
            'ao' => 'ao',
            'aq' => 'aq',
            'ar' => 'ar',
            'as' => 'as',
            'at' => 'at',
            'au' => 'au',
            'aw' => 'aw',
            'ax' => 'ax',
            'az' => 'az',
            'ba' => 'ba',
            'bb' => 'bb',
            'bd' => 'bd',
            'be' => 'be',
            'bf' => 'bf',
            'bg' => 'bg',
            'bh' => 'bh',
            'bi' => 'bi',
            'bj' => 'bj',
            'bl' => 'bl',
            'bm' => 'bm',
            'bn' => 'bn',
            'bo' => 'bo',
            'bq' => 'bq',
            'br' => 'br',
            'bs' => 'bs',
            'bt' => 'bt',
            'bv' => 'bv',
            'bw' => 'bw',
            'by' => 'by',
            'bz' => 'bz',
            'ca' => 'ca',
            'cc' => 'cc',
            'cd' => 'cd',
            'cf' => 'cf',
            'cg' => 'cg',
            'ch' => 'ch',
            'ci' => 'ci',
            'ck' => 'ck',
            'cl' => 'cl',
            'cm' => 'cm',
            'cn' => 'cn',
            'co' => 'co',
            'cr' => 'cr',
            'cu' => 'cu',
            'cv' => 'cv',
            'cw' => 'cw',
            'cx' => 'cx',
            'cy' => 'cy',
            'cz' => 'cz',
            'de' => 'de',
            'dj' => 'dj',
            'dk' => 'dk',
            'dm' => 'dm',
            'do' => 'do',
            'dz' => 'dz',
            'ec' => 'ec',
            'ee' => 'ee',
            'eg' => 'eg',
            'eh' => 'eh',
            'er' => 'er',
            'es' => 'es',
            'et' => 'et',
            'fi' => 'fi',
            'fj' => 'fj',
            'fk' => 'fk',
            'fm' => 'fm',
            'fo' => 'fo',
            'fr' => 'fr',
            'ga' => 'ga',
            'gb' => 'gb',
            'gd' => 'gd',
            'ge' => 'ge',
            'gf' => 'gf',
            'gg' => 'gg',
            'gh' => 'gh',
            'gi' => 'gi',
            'gl' => 'gl',
            'gm' => 'gm',
            'gn' => 'gn',
            'gp' => 'gp',
            'gq' => 'gq',
            'gr' => 'gr',
            'gs' => 'gs',
            'gt' => 'gt',
            'gu' => 'gu',
            'gw' => 'gw',
            'gy' => 'gy',
            'hk' => 'hk',
            'hm' => 'hm',
            'hn' => 'hn',
            'hr' => 'hr',
            'ht' => 'ht',
            'hu' => 'hu',
            'id' => 'id',
            'ie' => 'ie',
            'il' => 'il',
            'im' => 'im',
            'in' => 'in',
            'io' => 'io',
            'iq' => 'iq',
            'ir' => 'ir',
            'is' => 'is',
            'it' => 'it',
            'je' => 'je',
            'jm' => 'jm',
            'jo' => 'jo',
            'jp' => 'jp',
            'ke' => 'ke',
            'kg' => 'kg',
            'kh' => 'kh',
            'ki' => 'ki',
            'km' => 'km',
            'kn' => 'kn',
            'kp' => 'kp',
            'kr' => 'kr',
            'kw' => 'kw',
            'ky' => 'ky',
            'kz' => 'kz',
            'la' => 'la',
            'lb' => 'lb',
            'lc' => 'lc',
            'li' => 'li',
            'lk' => 'lk',
            'lr' => 'lr',
            'ls' => 'ls',
            'lt' => 'lt',
            'lu' => 'lu',
            'lv' => 'lv',
            'ly' => 'ly',
            'ma' => 'ma',
            'mc' => 'mc',
            'md' => 'md',
            'me' => 'me',
            'mf' => 'mf',
            'mg' => 'mg',
            'mh' => 'mh',
            'mk' => 'mk',
            'ml' => 'ml',
            'mm' => 'mm',
            'mn' => 'mn',
            'mo' => 'mo',
            'mp' => 'mp',
            'mq' => 'mq',
            'mr' => 'mr',
            'ms' => 'ms',
            'mt' => 'mt',
            'mu' => 'mu',
            'mv' => 'mv',
            'mw' => 'mw',
            'mx' => 'mx',
            'my' => 'my',
            'mz' => 'mz',
            'na' => 'na',
            'nc' => 'nc',
            'ne' => 'ne',
            'nf' => 'nf',
            'ng' => 'ng',
            'ni' => 'ni',
            'nl' => 'nl',
            'no' => 'no',
            'np' => 'np',
            'nr' => 'nr',
            'nu' => 'nu',
            'nz' => 'nz',
            'om' => 'om',
            'pa' => 'pa',
            'pe' => 'pe',
            'pf' => 'pf',
            'pg' => 'pg',
            'ph' => 'ph',
            'pk' => 'pk',
            'pl' => 'pl',
            'pm' => 'pm',
            'pn' => 'pn',
            'pr' => 'pr',
            'ps' => 'ps',
            'pt' => 'pt',
            'pw' => 'pw',
            'py' => 'py',
            'qa' => 'qa',
            're' => 're',
            'ro' => 'ro',
            'rs' => 'rs',
            'ru' => 'ru',
            'rw' => 'rw',
            'sa' => 'sa',
            'sb' => 'sb',
            'sc' => 'sc',
            'sd' => 'sd',
            'se' => 'se',
            'sg' => 'sg',
            'sh' => 'sh',
            'si' => 'si',
            'sj' => 'sj',
            'sk' => 'sk',
            'sl' => 'sl',
            'sm' => 'sm',
            'sn' => 'sn',
            'so' => 'so',
            'sr' => 'sr',
            'ss' => 'ss',
            'st' => 'st',
            'sv' => 'sv',
            'sx' => 'sx',
            'sy' => 'sy',
            'sz' => 'sz',
            'tc' => 'tc',
            'td' => 'td',
            'tf' => 'tf',
            'tg' => 'tg',
            'th' => 'th',
            'tj' => 'tj',
            'tk' => 'tk',
            'tl' => 'tl',
            'tm' => 'tm',
            'tn' => 'tn',
            'to' => 'to',
            'tr' => 'tr',
            'tt' => 'tt',
            'tv' => 'tv',
            'tw' => 'tw',
            'tz' => 'tz',
            'ua' => 'ua',
            'ug' => 'ug',
            'um' => 'um',
            'us' => 'us',
            'uy' => 'uy',
            'uz' => 'uz',
            'va' => 'va',
            'vc' => 'vc',
            've' => 've',
            'vg' => 'vg',
            'vi' => 'vi',
            'vn' => 'vn',
            'vu' => 'vu',
            'wf' => 'wf',
            'ws' => 'ws',
            'ye' => 'ye',
            'yt' => 'yt',
            'za' => 'za',
            'zm' => 'zm',
            'zw' => 'zw',
        ),
      );
  }

  function linksConvertFilter($content, $separator, $locale, $routeName, $routeIdName, $extraParams)
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
              $curLink = '<a href="'.$this->container->get('router')->generate($routeName, array_merge($extraParams, array($routeIdName => $curResult->getId()))).'">'.$curLink.'</a>';
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