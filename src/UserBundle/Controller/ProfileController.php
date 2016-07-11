<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Form\ProfileType;
use FOS\UserBundle\Mailer\Mailer;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileController extends Controller
{
    public $route;

    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $lastMail = $user->getEmail();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        $this->route = 'homePage';
        if ($form->isSubmitted() && $form->isValid()) {
            if($lastMail != $user->getEmail()) {
                $user->setEnabled(false);
                $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
                $mailer = $this->container->get('fos_user.mailer');
                $mailer->sendConfirmationEmailMessage($user);
                $this->get('security.token_storage')->setToken(null);
                $this->get('request')->getSession()->invalidate();
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $this->route = 'fos_user_registration_check_email';
            }
            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($user, true);
            $url = $this->container->get('router')->generate($this->route);
            return new RedirectResponse($url);
        }

        return $this->render('UserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
