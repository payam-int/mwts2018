<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InstallFormType;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class InstallController extends Controller
{
    /**
     * @Route("/install", name="install")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthorizationCheckerInterface $authChecker, KernelInterface $kernel)
    {
        $users = [];
        try {
            $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        } catch (\Exception $e) {
            // ignore
        }
        if (count($users) > 0) {
            throw new AccessDeniedException('Access denied !');
        }


        $install_completed = false;

        $user = new User();

        $user->setFirstName('Admin');
        $user->setLastname('Admin');
        $user->setNationalCode('0000000000');
        $user->setPhoneNumber('09120000000');

        $form = $this->createForm(InstallFormType::class, $user);

        $conn = $this->getDoctrine()->getConnection();
        $em = $this->getDoctrine()->getManager();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        try {
            $table_name = $em->getClassMetadata(User::class)->getTableName();
            $conn->executeQuery(sprintf("SELECT 1 from %s", $table_name));
        } catch (\Exception $e) {
            $input = new ArrayInput(array(
                'command' => 'doctrine:schema:create',
            ));
            $output = new BufferedOutput();
            $application->run($input, $output);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(['ROLE_ADMIN']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $install_completed = true;
        }

        return $this->render('install.html.twig', [
            "form" => $form->createView(),
            "install_completed" => $install_completed
        ]);
    }
}
