<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\ResetPassword;
use App\Response\ApiSuccessResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends BaseController
{

    /**
     * @Route("/login", name="admin_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [

            'client_id' => true,

            // parameters usually defined in Symfony login forms
            'error' => $error,
            'last_username' => $lastUsername,

            // OPTIONAL parameters to customize the login form:

            // the translation_domain to use (define this option only if you are
            // rendering the login template in a regular Symfony controller; when
            // rendering it from an EasyAdmin Dashboard this is automatically set to
            // the same domain as the rest of the Dashboard)
            'translation_domain' => 'admin',

            // by default EasyAdmin displays a black square as its default favicon;
            // use this method to display a custom favicon: the given path is passed
            // "as is" to the Twig asset() function:
            // <link rel="shortcut icon" href="{{ asset('...') }}">
            'favicon_path' => '/favicon-admin.svg',

            // the title visible above the login form (define this option only if you are
            // rendering the login template in a regular Symfony controller; when rendering
            // it from an EasyAdmin Dashboard this is automatically set as the Dashboard title)
            'page_title' => 'LabOwl Admin login',

            // the string used to generate the CSRF token. If you don't define
            // this parameter, the login form won't include a CSRF token
            'csrf_token_intention' => 'authenticate',

            // the URL users are redirected to after the login (default: '/admin')
            'target_path' => '/',//$this->generateUrl('admin'),

            // the label displayed for the username form field (the |trans filter is applied to it)
            'username_label' => 'E-Mail-Adresse',


            // the label displayed for the Sign In form button (the |trans filter is applied to it)
            'sign_in_label' => 'Einloggen',

            // the label displayed for the Demo Registration modal button (the |trans filter is applied to it)
            'demo_registration_label' => 'Testversion anfordem',

            // the 'name' HTML attribute of the <input> used for the username field (default: '_username')
            'username_parameter' => '_username',

            // the 'name' HTML attribute of the <input> used for the password field (default: '_password')
            'password_parameter' => '_password',

            // whether to enable or not the "forgot password?" link (default: false)
            'forgot_password_enabled' => true,

            // the path (i.e. a relative or absolute URL) to visit when clicking the "forgot password?" link (default: '#')
            'forgot_password_path' => $this->generateUrl('forgot-password'),

            // the label displayed for the "forgot password?" link (the |trans filter is applied to it)
            'forgot_password_label' => 'Passwort zurücksetzen?',

            // whether to enable or not the "remember me" checkbox (default: false)
            'remember_me_enabled' => true,

            // remember me name form field (default: '_remember_me')
            'remember_me_parameter' => 'labOwl_remember_me',

            // whether to check by default the "remember me" checkbox (default: false)
            'remember_me_checked' => true,

            // whether to check by default the "accept dec" checkbox (default: false)
            'accept_dec_checked' => true,

            // the label displayed for the remember me checkbox (the |trans filter is applied to it)
            'remember_me_label' => 'Remember me',

            // the label displayed for the accept dec checkbox (the |trans filter is applied to it)
            'accept_dec_label' => 'Ja, ich akzeptiere die Allgemeinen Geschäftsbedingungen für Stride Energy der KunVerGmbH',
        ]);
    }

    /**
     * @Route("/forgot-password", name="forgot-password", methods={"GET"})
     */
    public function forgotPassword(): Response
    {
        return $this->render('login/forgot-password.html.twig',[]);
    }

    #[Route("/forgot-password-post", name:"forgot-password-post", methods:["POST"])]
    public function forgotPasswordPost(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $email = $request->getPayload()->get('email');
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $this->addFlash('error', 'User not found');
            return $this->render('login/forgot-password.html.twig', []);
        }

        $resetPassword = $entityManager->getRepository(ResetPassword::class)->findOneBy(['email' => $email]);
        # generate hash
        $resetCode = hash('sha256', $email . time());

        if ($resetPassword) {
            $entityManager->remove($resetPassword);
            $entityManager->flush();
        }

        $newResetPassword = new ResetPassword();
        $newResetPassword->setEmail($email);
        $newResetPassword->setOtp($resetCode);
        $newResetPassword->setCreatedAt(new \DateTimeImmutable());
        $newResetPassword->setExpiresAt(new \DateTimeImmutable('+2 hour'));

        $entityManager->persist($newResetPassword);
        $entityManager->flush();

        // generate url route 'validate-reset-code' with code
        $url = $this->generateUrl('validate-reset-code', ['code' => $resetCode], UrlGeneratorInterface::ABSOLUTE_URL);
        $email = (new Email())
            ->from('noreply@labowl.cloud')
            ->to($email)
            ->subject('OTP code for Password Reset')
            ->text('<h1>Your password reset code is: </h1>' . $resetCode . '<br>Go to ' . $url . ' to reset your password.');

        try {
            $mailer->send($email);

            $this->addFlash('success', 'Email sent successfully.');
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'Failed to send email.');
        }

        return $this->redirectToRoute('admin_login');
    }

    /**
     * @Route("/validate-reset-code/{code}", name="validate-reset-code")
     */
    public function validateResetCode($code, EntityManagerInterface $entityManager): Response
    {
        $resetPassword = $entityManager->getRepository(ResetPassword::class)->findOneBy(['otp' => $code]);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $resetPassword->getEmail()]);

        if (!$user) {
            $this->addFlash('error', 'Invalid Reset Code');
            return $this->redirectToRoute('forgot-password');
        }

        $now = new \DateTime();
        if ($resetPassword->getExpiresAt() <= $now) {
            $this->addFlash('error', 'Reset Code has expired');
            return $this->redirectToRoute('forgot-password');
        }

        return $this->render('login/forgot-password-new.html.twig', ['token' => $code, 'email' => $resetPassword->getEmail()]);
    }

    
    #[Route("/forgot-password-new-post", name: "forgot-password-new-post", methods: ["POST"])]
    public function forgotPasswordNewPost(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Example: Get data from request. Adjust according to your form's input names.
        $email = $request->request->get('email');
        $token = $request->request->get('token');
        $newPassword = $request->request->get('password');

        // Find the reset password entry using the token
        $resetPasswordEntry = $entityManager->getRepository(ResetPassword::class)->findOneBy(['otp' => $token]);

        if (!$resetPasswordEntry) {
            // Handle error - token not found
            $this->addFlash('error', 'The token is invalid or expired.');
            return $this->redirectToRoute('forgot-password');
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $resetPasswordEntry->getEmail()]);
        if (!$user) {
            // Handle error - user not found
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('forgot-password');
        }

        // Update the user's password
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $entityManager->flush();

        // Remove the reset password token to prevent reuse
        $entityManager->remove($resetPasswordEntry);
        $entityManager->flush();

        // Success - Redirect to login or another appropriate page
        $this->addFlash('success', 'Ihr Passwort wurde erfolgreich zurückgesetzt. Sie können sich jetzt mit Ihrem neuen Passwort anmelden.');
        return $this->redirectToRoute('admin_login');
    }

    #[Route("/demo-registration-post", name: "demo-registration-post", methods: ["POST"])]
    public function demoRegistration(){
        var_dump("call success!");
    }

    /**
     * @Route("/reset-password-post", name="reset-password-post", methods={"POST"})
     */
    public function resetPasswordPost(Request $request, EntityManagerInterface $entityManager, UserPasswordHasher $hasher): Response
    {
        $email = $request->attributes->get('email');
        $password = $request->attributes->get('password');
        $repeatPassword = $request->attributes->get('repeat_password');

        if ($password !== $repeatPassword) {
            $this->addFlash('error', 'Passwords do not match.');
            return $this->redirectToRoute('reset-password', ['email' => $email]);
        }

        $resetPassword = $entityManager->getRepository(ResetPassword::class)->findOneBy(['email' => $email]);
        if ($resetPassword) {
            $entityManager->remove($resetPassword);
            $entityManager->flush();
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $user->setPassword($hasher->hashPassword($user, $password));
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_login');
    }

    /**
     * @Route("/reset-password", name="reset-password")
     */
    public function resetPassword(): Response
    {
        return $this->render('login/reset-password.html.twig',[]);
    }

    /**
     * @Route("/api/login", name="app_login", methods={"POST"})
     */
    public function apiLogin(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
            ], 400);
        }
        return $this->json([
                'user' => $this->getUser() && $this->getUser()->getId() ? $this->getUser()->getId() : null]
        );
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        // redirect to admin_login
        return $this->redirectToRoute('admin_login');
        //throw new \Exception('never should be reached');
    }

}
