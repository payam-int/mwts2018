<?php

namespace App\Controller;

use App\Entity\ResetPasswordToken;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ResetPasswordType;
use App\Form\UserLoginType;
use App\Form\UserRegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthenticationController extends Controller
{
    /**
     * @Route("/authentication", name="authentication")
     */
    public function index()
    {
        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', ['path' => str_replace($this->getParameter('kernel.project_dir') . '/', '', __FILE__)]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authUtils, AuthorizationCheckerInterface $authChecker)
    {


        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY'))
            return $this->redirectToRoute('profile');

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();
        if ($error != null)
            $error = $error->getMessage();

        $user = new User();
        $form = $this->createForm(UserLoginType::class, $user);
        $form->handleRequest($request);

        return $this->render('admin.login.html.twig', array(
            'form' => $form->createView(),
            'error' => $error,
        ));
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY'))
            return $this->redirectToRoute('profile');

        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->login_after_register($request, $user);
            return $this->redirectToRoute('home');
        }


//        print_r(get_class($form->createView()));
        return $this->render(
            'admin.register.html.twig',
            array('form' => $form->createView())
        );

    }

    private function login_after_register(Request $request, User $user)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());

        $this->get("security.token_storage")->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * @Route("/user/reset", name="reset_password")
     */
    public function resetPassword(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ResetPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $submitted = $form->getData();
            $email = $submitted['email'];

            $em = $this->getDoctrine()->getManager();
            $usersRepository = $this->getDoctrine()->getRepository(User::class);
            $user = $usersRepository->findByEmail($email);
            if ($user != null) {
                $hash = new ResetPasswordToken($user);
                $em->persist($hash);
                $em->flush();

                $reset_url = $this->generateUrl('reset_password_token', ['token' => $hash->getHash()], UrlGeneratorInterface::ABSOLUTE_URL);

                $message = (new \Swift_Message('Hello Email'))
                    ->setFrom('send@example.com')
                    ->setTo('recipient@example.com')
                    ->setBody(
                        $this->renderView('emails/password_reset.html.twig', ['loginUrl' => $reset_url, 'hash' => $hash]),
                        'text/html'
                    )->addPart(
                        $this->renderView('emails/password_reset.txt.twig', ['loginUrl' => $reset_url, 'hash' => $hash]),
                        'text/plain'
                    );

                $mailer->send($message);

                return $this->renderView('emails/password_reset.txt.twig', ['loginUrl' => $reset_url, 'hash' => $hash]);

            }
        }

        return $this->render('admin.reset_password.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/user/reset/{token}", name="reset_password_token")
     * @Entity("hash", expr="repository.find(token)")
     */
    public function resetPasswordToken(Request $request, ResetPasswordToken $hash)
    {
        $this->login_after_register($request, $hash->getUser());

        return $this->redirectToRoute('password_change');
    }

    /**
     * @Route("/user/password/change", name="password_change")
     */
    public function changePassword(Request $request)
    {
        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            if ($data['password'] != $data['password2']) {
                $form->addError(new FormError('Passwords are not the same.'));
            }
            if ($form->isValid()) {
                $user = $this->getUser();
                $user->setPlainPassword($data['password']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }

        return $this->render('admin.change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
