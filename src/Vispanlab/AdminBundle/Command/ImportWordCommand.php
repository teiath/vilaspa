<?php
namespace Vispanlab\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Sunra\PhpSimple\HtmlDomParser;
use Vispanlab\SiteBundle\Entity\Concept;
use Vispanlab\SiteBundle\Entity\Definition;

class ImportWordCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vispanlab:importword')
            ->setDescription('Import word data')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting ImportWord process');
        $htmlStr = file_get_contents('C:\Users\Niral\Desktop\topografia\yliko\html\p5_2_kthm.html');
        $dom = HtmlDomParser::str_get_html( $htmlStr );

        // TODO fetch actual gnwstiko antikeimno instead of default
        $poleodomia = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\AreaOfExpertise')->find(3);

        foreach($dom->find('table') as $curTable) {
            $concept = new Concept();
            $concept->getAreasofexpertise()->add($poleodomia); // Moved to LinkConceptsToAreasCommand
            // TR Structure:
            // 0 is GR Name
            // 1 is Alternative Names (EN, FR, DE)
            // 2 is Empty
            $this->parseTitle($curTable->find('tr', 0)->plaintext, $concept, 'el');
            if($concept->getNameForLang('el') == null) { continue; }
            // Ensure concept doesn't exist already
            $existingConcept = $this->getContainer()->get('doctrine')->getRepository('Vispanlab\SiteBundle\Entity\Definition')->findOneBy(array(
                'text' => $concept->getNameForLang('el')->getText(),
            ));
            if(isset($existingConcept)) {
                $output->writeln('Skipped concept: '.$concept->getNameForLang('el')->getTextFormatted());
                continue;
            }
            // End existing check
            $this->parseTitle($curTable->find('tr', 1)->find('td', 0)->plaintext, $concept, 'en');
            $this->parseTitle($curTable->find('tr', 1)->find('td', 1)->plaintext, $concept, 'fr');
            $this->parseTitle($curTable->find('tr', 1)->find('td', 2)->plaintext, $concept, 'de');
            $i = 0;

            $this->parseDefinition($curTable->find('tr', 2)->find('td', 0)->innertext, $concept, 'el');
            $this->parseDefinition($curTable->find('tr', 3)->find('td', 0)->innertext, $concept, 'en');
            $this->parseAlternativeDefinition($curTable->find('tr', 4)->find('td', 0)->innertext, $concept, 'el');
            $this->parseRelatedConcepts($curTable->find('tr', 5)->find('td', 0)->innertext, $concept, 'el');
            $this->parseComments($curTable->find('tr', 7)->find('td', 0)->innertext, $concept, 'el');
            foreach($curTable->find('tr') as $curTr) {
                if($i++ < 8) { continue; }
                $type = $this->detectType($curTr);
                if($type['type'] == 'ignore') { continue; }
                if($type['type'] == 'definition') {
                    /*if(!$concept->hasDefinitionForLang($type['locale'])) {
                        $this->parseDefinition($type['text'], $concept, $type['locale']);
                    } else {
                        $this->parseAlternativeDefinition($type['text'], $concept, $type['locale']);
                    }*/
                } else if($type['type'] == 'relatedConcept') {
                    //$this->parseRelatedConcepts($type['text'], $concept, $type['locale']);
                } else if($type['type'] == 'comments') {
                    //$this->parseComments(strip_tags($type['text']), $concept);
                }
            }
            $this->getContainer()->get('doctrine')->getManager()->persist($concept);
            $this->getContainer()->get('doctrine')->getManager()->flush($concept);
            $output->writeln('Added concept: '.$concept->getNameForLang('el')->getTextFormatted());
        }

        // Remove multiple spcaes
        // UPDATE `definition`
        // SET text_formatted = REPLACE( REPLACE( REPLACE( text_formatted, "  ", " " ), "  ", " " ) "  ", " " )
        // WHERE `concept_name_id` IS NOT NULL
        $output->writeln('Completed ImportWord process');
    }

    private function parseTitle($domPlainText, Concept &$concept, $locale) {
        // Remove EN: etc
        if($locale == 'el') { $pos = strpos($domPlainText, '.'); $domPlainText = substr($domPlainText, $pos+1); }
        $domPlainText = str_replace(strtoupper($locale).':', '', $domPlainText);
        $domPlainText = $this->mb_trim($domPlainText, ' '.PHP_EOL);
        if(mb_strlen($domPlainText) <= 0) { return; }
        $definition = new Definition();
        $definition->setFormat_type('richhtml');
        $definition->setLocale($locale);
        $definition->setConceptAsName($concept);
        $definition->setText($domPlainText);
        $definition->setText_formatted($definition->getText());
        $concept->addName($definition);
    }

    private function parseDefinition($domPlainText, Concept &$concept, $locale) {
        $domPlainText = $this->mb_trim($domPlainText, ' '.PHP_EOL);
        if(mb_strlen($domPlainText) <= 0) { return; }
        $definition = new Definition();
        $definition->setFormat_type('rawhtml');
        $definition->setLocale($locale);
        $definition->setConceptAsDefinition($concept);
        $definition->setText($domPlainText);
        $definition->setText_formatted($definition->getText());
        $concept->addDefinition($definition);
    }

    private function parseAlternativeDefinition($domPlainText, Concept &$concept, $locale) {
        $domPlainText = $this->mb_trim($domPlainText, ' '.PHP_EOL);
        if(mb_strlen($domPlainText) <= 0) { return; }
        $definition = new Definition();
        $definition->setFormat_type('rawhtml');
        $definition->setLocale($locale);
        $definition->setConceptAsAlternativeDefinition($concept);
        $definition->setText($domPlainText);
        $definition->setText_formatted($definition->getText());
        $concept->addAlternativeDefinitions($definition);
    }

    private function parseRelatedConcepts($domPlainText, Concept &$concept, $locale) {
        $domPlainText = $this->mb_trim($domPlainText, ' '.PHP_EOL);
        if(mb_strlen($domPlainText) <= 0) { return; }
        $definition = new Definition();
        $definition->setFormat_type('rawhtml');
        $definition->setLocale($locale);
        $definition->setConceptAsRelatedConcept($concept);
        $definition->setText($domPlainText);
        $definition->setText_formatted($definition->getText());
        $concept->addRelatedConcepts($definition);
    }

    private function parseComments($domPlainText, Concept &$concept) {
        $domPlainText = $this->mb_trim($domPlainText, ' '.PHP_EOL);
        if(mb_strlen($domPlainText) <= 0) { return; }
        $concept->setComments($domPlainText);
    }

    private function detectType($el) {
        $plaintext = $el->innertext;
        $locale = $this->getTextLanguage($el->plaintext, 'el');
        // Ignore empty
        if(mb_strlen($this->mb_trim($el->plaintext, ' '.PHP_EOL)) <= 0) { return array('type' => 'ignore', 'text' => str_replace('ΕΙΚΟΝΙΚΗ ΑΝΑΠΑΡΑΣΤΑΣΗ', '', $plaintext), 'locale' => $locale); }
        // End ignore empty
        if(mb_strpos($plaintext, 'ΣΥΝΑΦΕΙΣ ΕΝΝΟΙΕΣ') !== false) { return array('type' => 'relatedConcept', 'text' => str_replace('ΣΥΝΑΦΕΙΣ ΕΝΝΟΙΕΣ', '', $plaintext), 'locale' => $locale); }
        if(mb_strpos($plaintext, 'ΕΙΚΟΝΙΚΗ ΑΝΑΠΑΡΑΣΤΑΣΗ') !== false) { return array('type' => 'ignore', 'text' => str_replace('ΕΙΚΟΝΙΚΗ ΑΝΑΠΑΡΑΣΤΑΣΗ', '', $plaintext), 'locale' => $locale); }
        if(mb_strpos($plaintext, 'ΠΑΡΑΤΗΡΗΣΕΙΣ') !== false) { return array('type' => 'comments', 'text' => str_replace('ΠΑΡΑΤΗΡΗΣΕΙΣ', '', $plaintext), 'locale' => $locale); }
        return array('type' => 'definition', 'text' => $plaintext, 'locale' => $locale);
    }

    // Util functions
    private function mb_trim ($string, $charlist = null) {
        $charlist = preg_quote ($charlist, '/');

        if (is_null($charlist)) {
            return trim ($string);
        } else {
            return preg_replace ("/(^[$charlist]+)|([$charlist]+$)/us", '', $string);
        }
    }

    function getTextLanguage($text, $default) {
      $supported_languages = array(
          'en',
          'fr',
          'de',
      );
      // German word list
      // from http://wortschatz.uni-leipzig.de/Papers/top100de.txt
      $wordList['de'] = array ('der', 'die', 'und', 'in', 'den', 'von',
          'zu', 'das', 'mit', 'sich', 'des', 'auf', 'für', 'ist', 'im',
          'dem', 'nicht', 'ein', 'Die', 'eine');
      // English word list
      // from http://en.wikipedia.org/wiki/Most_common_words_in_English
      $wordList['en'] = array ('the', 'be', 'to', 'of', 'and', 'a', 'in',
          'that', 'have', 'I', 'it', 'for', 'not', 'on', 'with', 'he',
          'as', 'you', 'do', 'at');
      // French word list
      // from http://french.about.com/od/vocabulary/ss/mostcommonwords.htm
      $wordList['fr'] = array ('le', 'la', 'l\'', 'les', 'être', 'avoir', 'de',
          'un', 'une', 'des', 'je', 'il', 'ce', 'pas', 'à', 'ne', 'vous', 'qui', 'aller', 'faire');
      // clean out the input string - note we don't have any non-ASCII
      // characters in the word lists... change this if it is not the
      // case in your language wordlists!
      $text = preg_replace("/[^A-Za-z]/", ' ', $text);
      // count the occurrences of the most frequent words
      foreach ($supported_languages as $language) {
        $counter[$language]=0;
      }
      for ($i = 0; $i < 20; $i++) {
        foreach ($supported_languages as $language) {
          $counter[$language] = $counter[$language] +
            // I believe this is way faster than fancy RegEx solutions
            substr_count($text, ' ' .$wordList[$language][$i] . ' ');;
        }
      }
      // get max counter value
      // from http://stackoverflow.com/a/1461363
      $max = max($counter);
      $maxs = array_keys($counter, $max);
      // if there are two winners - fall back to default!
      if (count($maxs) == 1) {
        $winner = $maxs[0];
        $second = 0;
        // get runner-up (second place)
        foreach ($supported_languages as $language) {
          if ($language <> $winner) {
            if ($counter[$language]>$second) {
              $second = $counter[$language];
            }
          }
        }
        // apply arbitrary threshold of 10%
        if (($second / $max) < 0.1) {
          return $winner;
        }
      }
      return $default;
    }
}