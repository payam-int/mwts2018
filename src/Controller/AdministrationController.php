<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministrationController extends BaseAdminController

{
    /**
     * @Route("/download_file", name="download_file")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function download_file(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('App:' . $request->query->get('entity'));

        $id = $request->query->get('id');
        $entity = $repository->find($id);

        return new RedirectResponse($entity->getFile());
    }

    /**
     * @Route("/article/checking", name="checking_article")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function checking_article(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('App:' . $request->query->get('entity'));

        $id = $request->query->get('id');
        $entity = $repository->find($id);

        if ($entity->getState() != 'Checking')
            $entity->setState('Checking');
        else
            $entity->setState('Confirmed');
        $em->persist($entity);
        $em->flush();

        return new RedirectResponse($request->headers->get('referer'));
    }


}
