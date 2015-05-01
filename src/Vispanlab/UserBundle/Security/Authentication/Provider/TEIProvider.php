<?php
namespace Vispanlab\UserBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Vispanlab\UserBundle\Entity\User;

// http://blog.vandenbrand.org/2012/06/19/symfony2-authentication-provider-authenticate-against-webservice/
class TEIProvider extends UserAuthenticationProvider
{
    private $userProvider;

    public function __construct(UserCheckerInterface $userChecker, $providerKey, UserProviderInterface $userProvider, $hideUserNotFoundExceptions = true) {
        parent::__construct($userChecker, $providerKey, $hideUserNotFoundExceptions);
        $this->userProvider = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function retrieveUser($username, UsernamePasswordToken $token)
    {
        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            return $user;
        }

        try {
            $user = $this->userProvider->loadUserByUsernameAndPassword($username, $token->getCredentials());

            if (!$user instanceof UserInterface) {
                throw new AuthenticationServiceException('The user provider must return a UserInterface object.');
            }

            return $user;
        } catch (UsernameNotFoundException $notFound) {
            throw $notFound;
        } catch (\Exception $repositoryProblem) {
            throw new AuthenticationServiceException($repositoryProblem->getMessage(), $token, 0, $repositoryProblem);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        $currentUser = $token->getUser();
        $presentedPassword = $token->getCredentials();
        if ($currentUser instanceof UserInterface) {
            if ('' === $presentedPassword) {
                throw new BadCredentialsException(
                    'The password in the token is empty. You may forgive turn off `erase_credentials` in your `security.yml`'
                );
            }

            if (!$this->authApi($currentUser, $presentedPassword)) {
                throw new BadCredentialsException('The credentials were changed from another session.');
            }
        } else {
            if ('' === $presentedPassword) {
                throw new BadCredentialsException('The presented password cannot be empty.');
            }

            if (!$this->authApi($user, $presentedPassword)) {
                throw new BadCredentialsException('The presented password is invalid.');
            }
        }
    }

    private function authApi($user, $password) {
        return false;
        $datauser = 'username='.$user->getUsername();   // testuser
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
    }
}