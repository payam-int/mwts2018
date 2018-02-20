<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserLoginType;
use App\Form\UserRegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

            $registrationType = $user->getRegistrationType();
            $registrationRoles = $registrationType->getRoles();

            if ($registrationRoles != null) {
                $user->setRoles(array_merge($user->getRoles(), $registrationRoles));
            }

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

}
