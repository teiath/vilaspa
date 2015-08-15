<?php

namespace Vispanlab\ApiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Vispanlab\ApiBundle\Controller\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Noxlogic\RateLimitBundle\Annotation\RateLimit;
use FOS\UserBundle\Model\UserInterface;

class UserController extends ApiController {
    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Get the logged in user information.",
     *   output="Vispanlab\UserBundle\Entity\User"
     * )
     * @Secure(roles="ROLE_USER")
     */
    public function getUsersMeAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        return $this->api_response($user);
    }

    /**
     * @ApiDoc(
     *      resource=true,
     *      description="Change the logged in user information",
     *      output="Vispanlab\UserBundle\Entity\User",
     *      input={
     *          "class"="Vispanlab\UserBundle\Form\Type\ProfileFormType",
     *          "name"="fos_user_profile_form"
     *      },
     *      statusCodes={
     *          201="Returned when user is updated successfully",
     *          400="Returned on malformed request or missing data",
     *          401="Returned on request with non-authenticated user"
     *      }
     *)
     * @Secure(roles="ROLE_USER")
     */
    public function patchUsersMeAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (! $this->is_authenticated($user)) {
            return $this->api_error('Authentication required', 401);
        }
        $formHandler = $this->container->get('fos_user.profile.form.handler');
        $process = $formHandler->process($user);
        $uform = $this->container->get('fos_user.profile.form');
        if ($process) {
            $this->container->get('doctrine')->getManager()->persist($user);
            $this->container->get('doctrine')->getManager()->flush();
        } else {
            return $this->api_error($this->api_get_form_errors($uform), 400);
        }
        $this->container->get('doctrine')->getManager()->refresh($user);
        return $this->api_response($user, 201);
    }

    /**
     * @ApiDoc(
     *      resource=true,
     *      description="Change the logged in user's password",
     *      output="Vispanlab\UserBundle\Entity\User",
     *      input={
     *          "class"="FOS\UserBundle\Form\Type\ChangePasswordFormType",
     *          "name"="fos_user_change_password_form"
     *      },
     *      statusCodes={
     *          201="Returned when password is updated successfully",
     *          400="Returned on malformed request or missing data",
     *          401="Returned on request with non-authenticated user"
     *      }
     *)
     * @Secure(roles="ROLE_USER")
     */
    public function patchUsersMePasswordAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (! $this->is_authenticated($user)) {
            return $this->api_error('Authentication required', 401);
        }
        $formHandler = $this->container->get('fos_user.change_password.form.handler');
        $process = $formHandler->process($user);
        $uform = $this->container->get('fos_user.change_password.form');
        if ($process) {
            $this->container->get('doctrine')->getManager()->persist($user);
            $this->container->get('doctrine')->getManager()->flush();
        } else {
            return $this->api_error($this->api_get_form_errors($uform), 400);
        }
        $this->container->get('doctrine')->getManager()->refresh($user);
        return $this->api_response($user, 201);
    }

    /**
     * @ApiDoc(
     *      resource=true,
     *      description="Change the logged in user's password",
     *      statusCodes={
     *          204="Returned when reset request is sent successfully",
     *          400="Returned on malformed request or missing data",
     *          401="Returned when the email has already been requested recently",
     *          404="Returned when the email is not found",
     *      }
     *)
     */
    public function postUsersReset_passwordAction($email) {
        $username = $email;

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->api_error('invalid_email', 404);
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->api_error('already_requested', 401);
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set(\FOS\UserBundle\Controller\ResettingController::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);
        return $this->api_response('', 204);
    }

    protected function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }

    /**
     * @ApiDoc(
     *      resource=true,
     *      description="Register a new user",
     *      output="Vispanlab\UserBundle\Entity\User",
     *      input={
     *          "class"="Vispanlab\UserBundle\Form\Type\RegistrationFormType",
     *          "name"="fos_user_registration_form"
     *      },
     *      statusCodes={
     *          201="Returned when user is registered successfully",
     *          400="Returned on malformed request or missing data",
     *      }
     *)
     */
    public function postUsersAction() {
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();
            $this->container->get('doctrine')->getManager()->refresh($user);
        } else {
            return $this->api_error($this->api_get_form_errors($form), 400);
        }

        return $this->api_response($user, 201);
    }
}