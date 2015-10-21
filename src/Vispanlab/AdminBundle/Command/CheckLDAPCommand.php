<?php
namespace Vispanlab\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckLDAPCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vispanlab:checkldap')
            ->setDescription('Check LDAP web service')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting CheckLDAP process');
        $datauser = 'username=csteststd';   // testuser
        $datauser .= '&password=efhc7dc(';
        $datauser .= '&m=N11050';
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL,"http://195.130.109.174:8887/_api/giauth");
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $datauser);
        curl_setopt ($ch, CURLOPT_POST, 1);

        $result = curl_exec($ch);
        curl_close($ch);
        $i = json_decode($result, TRUE);
        if(!isset($i['csteststd']) || !isset($i['csteststd']['auth']) || $i['csteststd']['auth'] != 'yes') {
            throw new \Exception('Vispanlab LDAP error: '.var_export($result, true));
        }
    }
}