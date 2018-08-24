<?php

namespace AppBundle\Controller\Security;

use AppBundle\Entity\TeaGroup;
use AppBundle\Entity\User;
use AppBundle\Form\Security\LoginForm;
use AppBundle\Form\Security\RegisterForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller
{
    /**
     * @Route("/register", name="security_register")
     */
    public function registerAction(Request $request)
    {
        // create the registration form
        $formRegister = $this->createForm(RegisterForm::class);

        $formRegister->handleRequest($request);

        // validate the registration form
        if ($formRegister->isSubmitted() && $formRegister->isValid()) {

            /** @var TeaGroup $teaGroup */
            $teaGroup = new TeaGroup();
            $teaGroup->setCreatedAt(new \DateTime("now"));
            $teaGroup->setName($formRegister['groupName']->getData());

            /** @var User $user */
            $user = $formRegister->getData();
            $user->setCreatedAt(new \DateTime("now"));
            $user->setRoles(["ROLE_ADMIN"]);

            // create an array to set the preferences of the user
            $preferences[] = $formRegister['preferences']->getData();
            $user->setPreferences($preferences);

            $user->setTeaGroup($teaGroup);

            $em = $this->getDoctrine()->getManager();
            $em->persist($teaGroup);
            $em->persist($user);
            $em->flush();

            $this->addFlash('green', 'Welcome '.$user->getFirstName().' from '.$teaGroup->getName());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render(
            'security/register.html.twig',
            array(
                'formRegister' => $formRegister->createView()
            )
        );
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $formLoginError = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // create the login form
        $formLogin = $this->createForm(LoginForm::class, [
            '_username' => $lastUsername,
        ]);

        return $this->render(
            'security/login.html.twig',
            array(
                'formLogin' => $formLogin->createView(),
                'formLoginError' => $formLoginError
            )
        );
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('this should not be reached!');
    }
}