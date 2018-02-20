<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\SummaryArticle;
use App\Form\ArticleType;
use App\Form\SummaryArticleType;
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

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
//        $userPayments = $user->getPayments();

//        print($user->isPaymentDone());

        $user_can_send_articles = $authChecker->isGranted('ROLE_SEND_ARTICLE');
        $params = [];
        if ($user_can_send_articles) {
            $summaryRepository = $this->getDoctrine()->getRepository(SummaryArticle::class);
            $confimred_articles_querybuilder = $summaryRepository->getConfirmedSummaryArticlesByUserQueryBuilder($user);
            $has_confirmed_articles = $summaryRepository->hasConfirmedSummary($user);

            $summaryArticle = new SummaryArticle();
            $summaryArticle->setUser($user);

            $summaryForm = $this->createForm(SummaryArticleType::class, $summaryArticle);


            $article = new Article();
            $article->setUser($user);

            $articleForm = $this->createForm(ArticleType::class, $article, ["confirmed_query_builder" => $confimred_articles_querybuilder]);

            $articleForm->handleRequest($request);
            $summaryForm->handleRequest($request);

            if ($summaryForm->isSubmitted() && $summaryForm->isValid()) {
                $em->persist($summaryArticle);
                $em->flush();
            }
            if ($articleForm->isSubmitted() && $articleForm->isValid()) {
                $em->persist($article);
                $em->flush();
            }
            $params += [
                'summaryForm' => $summaryForm->createView(),
                'articleForm' => $articleForm->createView(),
                'showArticleForm' => $has_confirmed_articles
            ];
        }

        return $this->render('profile.html.twig', [
                'user' => $user,
                'can_send_articles' => $user_can_send_articles
            ] + $params);
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

}
