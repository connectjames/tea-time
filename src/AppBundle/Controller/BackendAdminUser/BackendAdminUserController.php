<?php

namespace AppBundle\Controller\BackendAdminUser;

use AppBundle\Entity\TeaGroup;
use AppBundle\Entity\User;
use AppBundle\Form\BackendAdminUser\MemberForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/tea-admin")
 */
class BackendAdminUserController extends Controller
{
    /**
     * @Route("/report", name="backend_admin_user_report")
     */
    public function reportAction()
    {
        return $this->render('backendAdminUser/report.html.twig');
    }

    /**
     * @Route("/members", name="backend_admin_user_member")
     */
    public function memberAction()
    {
        $em = $this->getDoctrine()->getManager();

        // find user online
        /** @var User $user */
        $user = $this->getUser();

        // find which group the user is part of
        /** @var TeaGroup $teaGroup */
        $teaGroup = $user->getTeaGroup();

        $members = $em->getRepository('AppBundle:User')->findBy(
            ['teaGroup' => $teaGroup],
            ['cupsTotal' => 'DESC']
        );

        return $this->render('backendAdminUser/member.html.twig', [
            'teaGroup' => $teaGroup,
            'members' => $members
        ]);
    }

    /**
     * @Route("/members/add", name="backend_admin_user_member_create")
     */
    public function createMemberAction(Request $request)
    {
        // find user online
        /** @var User $user */
        $user = $this->getUser();

        // find which group the user is part of
        /** @var TeaGroup $teaGroup */
        $teaGroup = $user->getTeaGroup();

        // create the registration form
        $formMember = $this->createForm(MemberForm::class);

        $formMember->handleRequest($request);

        // validate the registration form
        if ($formMember->isSubmitted() && $formMember->isValid()) {

            $user = $formMember->getData();
            $user->setCreatedAt(new \DateTime("now"));
            $user->setTeaGroup($teaGroup);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('green', 'New TEA member added.');

            $members = $em->getRepository('AppBundle:User')->findBy(
                ['teaGroup' => $teaGroup],
                ['cupsTotal' => 'DESC']
            );

            return $this->render('backendAdminUser/member.html.twig', [
                'teaGroup' => $teaGroup,
                'members' => $members
            ]);
        }

        return $this->render('backendAdminUser/addMember.html.twig', [
            'user' => $user,
            'teaGroup' => $teaGroup,
            'formMember' => $formMember->createView()
        ]);
    }

    /**
     * @Route("/members/level", name="backend_admin_user_level_member_update")
     */
    public function updateLevelMemberAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $member */
        $member = $em->getRepository('AppBundle:User')
            ->findOneBy(array(
                'id' => $request->query->get('memberId')
            ));

        // if member doesn't exist
        if (!$member) {
            throw $this->createNotFoundException('Member not found');
        }

        // change member level
        $member->setLevel((int)$request->query->get('level'));
        $em->persist($member);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * @Route("/members/activate/{memberId}", name="backend_admin_user_activate_member_update")
     */
    public function updateActivateMemberAction(Request $request, $memberId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $member */
        $member = $em->getRepository('AppBundle:User')
            ->find($memberId);

        // if member doesn't exist
        if (!$member) {
            throw $this->createNotFoundException('Member not found');
        }

        // activate user
        if ($request->query->get('activateStatus')) {
            if ($request->query->get('activateStatus') === 'activate') {
                $member->setActivate(true);
            } elseif ($request->query->get('activateStatus') === 'deactivate') {
                $member->setActivate(false);
            }
        }

        $em->persist($member);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * @Route("/members/delete/{memberId}", name="backend_admin_user_member_delete")
     */
    public function deleteMemberAction($memberId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $member */
        $member = $em->getRepository('AppBundle:User')
            ->find($memberId);

        // if member doesn't exist
        if (!$member) {
            throw $this->createNotFoundException('Member not found');
        }

        // delete user
        $em->remove($member);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * @Route("/deactivate", name="backend_admin_user_deactivate")
     */
    public function deactivateAction()
    {
        $em = $this->getDoctrine()->getManager();

        // find user online
        $user = $this->getUser();

        // find which group the user is part of
        /** @var TeaGroup $teaGroup */
        $teaGroup = $user->getTeaGroup();

        // deactivate user
        /** @var User $user */
        $user->setActivate(false);

        // deactivate all members of this Tea group
        foreach ($teaGroup->getMembers() as $member) {
            $member->setActivate(false);
            $em->persist($member);
        }

        $em->persist($user);
        $em->flush();

        $this->get('security.token_storage')->setToken(null);

        $contactPage = $this->generateUrl('home_contact');
        $this->addFlash('green', sprintf('We are sad to see go... If you want to reactivate your account don\'t hesitate to <a href="%s" title="Contact us">contact us</a>!', $contactPage));

        return $this->render('home/index.html.twig');
    }
}