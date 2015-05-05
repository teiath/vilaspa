<?php
namespace Vispanlab\UserBundle\Security;

use FOS\UserBundle\Security\EmailUserProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Vispanlab\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class TEIUserProvider extends EmailUserProvider
{
    public function loadUserByUsernameAndPassword($username, $password) {
        $user = $this->findUser($username);
        if ($user) {
            return $user;
        }

        $user = $this->findUserInTEI($username, $password);
        if($user) {
            return $user;
        }

        throw new UsernameNotFoundException(sprintf('No record found for user %s', $username));
    }

    private function findUserInTEI($username, $password) {
        $datauser = 'username='.$username;   // testuser
        $datauser .= '&password='.$password;
        $datauser .= '&m=N11050';
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL,"http://195.130.109.174:8887/_api/giauth");
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $datauser);
        curl_setopt ($ch, CURLOPT_POST, 1);

        $result = curl_exec($ch);
        curl_close($ch);
        $i = json_decode($result, TRUE);

        if(isset($i[$username]) && isset($i[$username]['auth']) && $i[$username]['auth'] == 'yes') {
            $user = new User();
            $user->setUsername('ldap_'.$username);
            $user->setEmail('ldap_'.$username.'@teiath.gr');
            $user->setEnabled(true);
            $user->setPassword(md5(rand(1, 100).'ldap_'.$username.'@teiath.gr'));
            $user->addRole('ROLE_STUDENT');
            return $user;
        }
        return null;
    }
}