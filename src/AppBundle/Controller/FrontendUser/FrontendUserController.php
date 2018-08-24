<?php

namespace AppBundle\Controller\FrontendUser;

use AppBundle\Entity\TeaGroup;
use AppBundle\Entity\User;
use AppBundle\Form\BackendAdminUser\TeaGroupForm;
use AppBundle\Form\Security\PasswordForm;
use AppBundle\Service\RandomUserSelectorService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/tea-round")
 */

class FrontendUserController extends Controller
{
    /**
     * @Route("/", name="frontend_user_index")
     */
    public function indexAction()
    {
        // find user online
        /** @var User $user */
        $user = $this->getUser();

        // is user activate ?
        if (!$user->getActivate()) {
            $this->get('security.token_storage')->setToken(null);

            $contactPage = $this->generateUrl('home_contact');
            $this->addFlash('yellow', sprintf('Sorry! Your account is deactivated. Please <a href="%s" title="Contact us">contact us</a> to reactivate it!', $contactPage));

            return $this->render('home/index.html.twig');
        }

        // find which group the user is part of
        /** @var TeaGroup $teaGroup */
        $teaGroup = $user->getTeaGroup();


        // STEP 1: Create new Tea round arrays
        $teaRoundRandArray = [];

        $teaRoundParticipants = [];

        // STEP 2: Add new Tea round participants
        $teaMember1 = [23 => 'tea'];
        $teaMember2 = [24 => 'tea'];

        array_push($teaRoundParticipants, $teaMember1, $teaMember2);

        // STEP 3 : Add new Tea round participants in array for shuffle
        $teaMember1 = 23; // level 55
        $level = 55;

        $teaMember1Array = array_fill(0, $level, $teaMember1);

        $teaMember2 = 24; // level 45
        $level = 45;

        $teaMember2Array = array_fill(0, $level, $teaMember2);

        $teaRoundRandArray = array_merge($teaMember1Array, $teaMember2Array);

        // STEP 3: Shuffle everything and print r
        $RandomSelector = new RandomUserSelectorService();
        $RandomSelectedUserKey = $RandomSelector->selectARandomUser($teaRoundRandArray);

        print_r($teaRoundRandArray[$RandomSelectedUserKey]);

        print_r($teaRoundParticipants);



        return $this->render('frontendUser/index.html.twig', [
            'user' => $user,
            'teaGroup' => $teaGroup
        ]);
    }

    /**
     * @Route("/my-preferences", name="frontend_user_preference")
     */
    public function preferenceAction()
    {
        // find user online
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('frontendUser/preference.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/my-preferences/add", name="frontend_user_preference-create")
     */
    public function createPreferenceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // find user online
        /** @var User $user */
        $user = $this->getUser();

        $preferences = $user->getPreferences();

        if ($request->query->get('newPreference')) {

            // validate new preference
            if ($request->query->get('newPreference') === htmlspecialchars((string)$request->query->get('newPreference'))) {

                // add preference to user
                $user->setPreferences(
                    array_push($preferences, htmlspecialchars((string)$request->query->get('newPreference')))
                );

                $em->persist($user);
                $em->flush();
            } else {

                // validation failed, show error
                return $this->render('frontendUser/preference/_savePreferenceError.html.twig', array(
                    'user' => $user
                ));
            }
        }

        return $this->render('frontendUser/preference/_savePreference.html.twig', array(
            'user' => $user
        ));
    }

    /**
     * @Route("/my-preferences/delete/{preferenceKey}", name="frontend_user_preference-delete")
     */
    public function deletePreferenceAction($preferenceKey)
    {
        $em = $this->getDoctrine()->getManager();

        // find user online
        /** @var User $user */
        $user = $this->getUser();

        $preferences = $user->getPreferences();

        // delete preference to user
        unset($preferences[(int)$preferenceKey]);

        $user->setPreferences($preferences);

        $em->persist($user);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * @Route("/my-details", name="frontend_user_detail")
     */
    public function detailAction(Request $request)
    {
        // find user online
        /** @var User $user */
        $user = $this->getUser();

        // find which group the user is part of
        /** @var TeaGroup $teaGroup */
        $teaGroup = $user->getTeaGroup();

        // create the registration form
        $formTeaGroup = $this->createForm(TeaGroupForm::class);

        $formTeaGroup->handleRequest($request);

        // validate the registration form
        if ($formTeaGroup->isSubmitted() && $formTeaGroup->isValid()) {

            /** @var TeaGroup $teaGroup */
            $teaGroup->setName((string)$formTeaGroup['name']->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($teaGroup);
            $em->flush();

            $this->addFlash('green', 'Tea group name updated!');
        }

        // create the password update form
        $formPassword = $this->createForm(PasswordForm::class);

        $formPassword->handleRequest($request);

        // validate the password change
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {

            $userPassword = $formPassword->getData();

            $userPassword = $userPassword["plainPassword"];

            $user->setPlainPassword($userPassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('green', 'Your Password has successfully been updated.');

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render('frontendUser/detail.html.twig', [
            'user' => $user,
            'teaGroup' => $teaGroup,
            'formTeaGroup' => $formTeaGroup->createView(),
            'formPassword' => $formPassword->createView()
        ]);
    }

    /**
     * @Route("/deactivate", name="frontend_user_deactivate")
     */
    public function deactivateAction()
    {
        $em = $this->getDoctrine()->getManager();

        // find user online
        $user = $this->getUser();

        // deactivate user
        /** @var User $user */
        $user->setActivate(false);

        $em->persist($user);
        $em->flush();

        $this->get('security.token_storage')->setToken(null);

        $contactPage = $this->generateUrl('home_contact');
        $this->addFlash('green', sprintf('We are sad to see go... If you want to reactivate your account don\'t hesitate to <a href="%s" title="Contact us">contact us</a>!', $contactPage));

        return $this->render('home/index.html.twig');
    }
}