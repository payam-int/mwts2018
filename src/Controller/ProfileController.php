<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Payment;
use App\Entity\SummaryArticle;
use App\Form\ArticleType;
use App\Form\SummaryArticleType;
use App\Form\UserInformationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProfileController extends Controller
{


    /**
     * @Route("/profile", name="profile")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function index(Request $request, AuthorizationCheckerInterface $authChecker)
    {
        return $this->render('profile_conf.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/profile/papers", name="profile_articles")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function articles(Request $request, AuthorizationCheckerInterface $authChecker)
    {

        $articles = $this->getDoctrine()->getRepository(SummaryArticle::class)->findByUser($this->getUser());
        return $this->render('profile_articles.html.twig', [
            'articles' => $articles,
            'papers' => $this->getParameter('papers')
        ]);
    }

    /**
     * @Route("/profile/papers/abstract/send", name="profile_articles_send")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function paper_abstract_send(Request $request, AuthorizationCheckerInterface $authChecker)
    {

        $user = $this->getUser();
        $summaryArticle = new SummaryArticle();
        $summaryArticle->setUser($user);
        $summaryForm = $this->createForm(SummaryArticleType::class, $summaryArticle);
        $summaryForm->handleRequest($request);

        if ($summaryForm->isSubmitted() && $summaryForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($summaryArticle);
            $em->flush();
            return $this->redirectToRoute('payment_abstract', ['id' => $summaryArticle->getId()]);
        }

        return $this->render('profile_articles_send.html.twig', [
            "form" => $summaryForm->createView(),
            'papers' => $this->getParameter('papers')
        ]);
    }

    /**
     * @Route("/profile/papers/{id}/send_paper", name="profile_papers_send")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Entity("abstract", expr="repository.find(id)")
     */
    public function paper_send(Request $request, SummaryArticle $abstract)
    {

        $user = $this->getUser();
        $paper = new Article();
        $paper->setUser($user);
        $paper->setSummary($abstract);
        $summaryForm = $this->createForm(ArticleType::class, $paper);
        $summaryForm->handleRequest($request);

        if ($summaryForm->isSubmitted() && $summaryForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $abstract->setArticle($paper);
            $em->persist($paper);
            $em->persist($abstract);
            $em->flush();
            return $this->redirectToRoute('payment_paper', ['id' => $paper->getId()]);
        }

        return $this->render('profile_paper_send.html.twig', [
            "form" => $summaryForm->createView(),
            'papers' => $this->getParameter('papers')
        ]);
    }


    /**
     * @Route("/profile/paper/{id}", name="profile_view_paper")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Entity("SummaryArticle", expr="repository.find(id)")
     */
    public function viewPaper(SummaryArticle $summaryArticle)
    {

        if ($summaryArticle->getUser() != $this->getUser())
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('profile_view_paper.html.twig', [
            'article' => $summaryArticle
        ]);
    }

    /**
     * @Route("/profile/payments", name="profile_payments")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function payments(Request $request, AuthorizationCheckerInterface $authChecker)
    {

        $payments = $this->getDoctrine()->getRepository(Payment::class)->findByUser($this->getUser());
        return $this->render('profile_payments.html.twig', [
            'payments' => $payments
        ]);
    }

    /**
     * @Route("/profile/edit", name="profile_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function edit(Request $request, AuthorizationCheckerInterface $authChecker)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserInformationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }


        return $this->render('profile_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
