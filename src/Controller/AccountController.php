<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     *
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $errors = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $errors !== null,
            'username' => $username,
        ]);
    }

    /**
     * @Route("/logout", name="account_logout")
     *
     * @return Response
     */
    public function logout()
    {

    }

    /**
     * register account
     *
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->IsSubmitted() && $form->IsValid()) {

            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créer, vous pouvez maintenant vous connecter !"
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * modif profile
     *
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function profil(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->IsSubmitted() && $form->IsValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données du profil ont bien été enregistrée avec succes"
            );
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * update Password
     *
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager)
    {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->IsSubmitted() && $form->IsValid()) {
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de pas actuel !"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    "success",
                    "Votre mot de passe a bien été modifié !"
                );

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * show my acoount
     *
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * forgetten Password
     *
     * @Route("/account/forget-password", name="account_forgetPassword")
     *
     * @return Response
     */
    public function forgettenPassword(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator,
        ObjectManager $manager,
        UserRepository $repo
    ) {

        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');
            $user = $repo->findOneByEmail($email);

            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('homepage');
            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $manager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('homepage');
            }

            $url = $this->generateUrl('app_reset_password', array('resetToken' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message())
                ->setSubject('SfBNB - Réinitialisé mot de passe')
                ->setFrom(['email@email.com' => 'SfBNB'])
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('account/email_resetting.html.twig', [
                        'url' => $url,
                        'fullname' => $user->getFullName(),
                        ]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash(
                'success',
                'Mail envoyé'
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('account/forgetten_password.html.twig');
    }

    /**
     * reset password
     *
     * @Route("/account/reset_password/{resetToken}", name="app_reset_password")
     *
     */
    public function resetPassword(
        Request $request,
        User $user,
        UserPasswordEncoderInterface $passwordEncoder,
        ObjectManager $manager,
        UserRepository $repo
    ) {
        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->remove('oldPassword');

        $form->handleRequest($request);

        if ($form->IsSubmitted() && $form->IsValid()) {
            $newPassword = $passwordUpdate->getNewPassword();
            $user->setHash($passwordEncoder->encodePassword($user, $newPassword));
            $user->setResetToken(null);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                "success",
                "Mot de passe mis à jour"
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/reset_password.html.twig', [
            'form' => $form->createView(),
            'fullname' => $user->getFullName(),
        ]);

    }
}
