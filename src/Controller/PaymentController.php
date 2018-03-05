<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Discount;
use App\Entity\Payment;
use App\Entity\SummaryArticle;
use App\Form\PaymentType;
use HttpResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SoapClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends Controller
{
    /**
     * @Route("/payment/abstract/{id}", name="payment_abstract")
     * @Entity("summary", expr="repository.find(id)")
     */
    public function payAbstract(Request $request, SummaryArticle $summary)
    {
        $papers = $this->getParameter('papers');
        $abstract_price = $papers['abstract']['price'];
        $abstract_discount = $papers['abstract']['discount'];

        $payment = new Payment($abstract_price, $this->getUser());

        $payment->setMetadata(['type' => 'abstract', 'id' => $summary->getId(), 'discount' => $abstract_discount]);

        $payment_form = $this->createForm(PaymentType::class, $payment);

        $payment_form->handleRequest($request);

        if ($payment_form->isSubmitted() && $payment_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);
            $em->flush();
            return $this->redirectToRoute('payment_pay', ['id' => $payment->getId()]);
        }

        return $this->render('payment_abstract.html.twig',
            [
                'form' => $payment_form->createView(),
                'payment' => $payment,
                'papers' => $papers
            ]);
    }

    /**
     * @Route("/payment/paper/{id}", name="payment_paper")
     * @Entity("summary", expr="repository.find(id)")
     */
    public function payPaper(Request $request, Article $article)
    {
        $papers = $this->getParameter('papers');
        $paper_price = $papers['paper']['price'];
        $paper_discount = $papers['paper']['discount'];

        $payment = new Payment($paper_price, $this->getUser());

        $payment->setMetadata(['type' => 'paper', 'id' => $article->getId(), 'discount' => $paper_discount]);

        $payment_form = $this->createForm(PaymentType::class, $payment);

        $payment_form->handleRequest($request);

        if ($payment_form->isSubmitted() && $payment_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);
            $em->flush();
            return $this->redirectToRoute('payment_pay', ['id' => $payment->getId()]);
        }

        return $this->render('payment_paper.html.twig',
            [
                'form' => $payment_form->createView(),
                'payment' => $payment,
                'papers' => $papers
            ]);
    }


    /**
     * @Route("/payment/pay/{id}", name="payment_pay")
     * @Entity("payment", expr="repository.find(id)")
     */
    public function pay(Request $request, Payment $payment)
    {
        if ($this->getParameter('payment_debug')) {
            return $this->redirectToRoute('payment_done', ['id' => $payment->getId()]);
        }
        if ($payment->getReferenceId() == null || $payment->getReferenceId() == '') {
            try {
                $this->getReferenceFor($payment);
            } catch (\Exception $e) {
                return $this->render('payment_error.html.twig', [
                    'message' => $e->getMessage()
                ]);
            }
        }

        if ($payment->getReferenceId() != null) {
            return new RedirectResponse(sprintf('https://payment.sharif.ir/research/submit.aspx?orderid=%s', $payment->getReferenceId()));
        }

        return null;

    }


    /**
     * @Route("/payment/attend", name="payment_attend")
     */
    public function payAttend()
    {
        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', ['path' => str_replace($this->getParameter('kernel.project_dir') . '/', '', __FILE__)]);
    }

    /**
     * @Route("/payment/done/{id}", name="payment_done")
     * @Entity("payment", expr="repository.find(id)")
     */
    public function paymentDone(Request $request, Payment $payment)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$payment->getDone() && $this->checkIfPaymentDone($payment)) {
            $payment->setDone(true);
            $payment->setDoneDate(new \DateTime());
            $meta = $payment->getMetadata();
            $em->persist($payment);
            if ($meta['type'] == 'abstract') {
                $summary = $this->getDoctrine()->getRepository(SummaryArticle::class)->find($meta['id']);
                $summary->setPayment($payment);
                $summary->setPaid(true);
                $em->persist($summary);
            } else if ($meta['type'] == 'paper') {
                $paper = $this->getDoctrine()->getRepository(Article::class)->find($meta['id']);
                $paper->setPayment($payment);
                $paper->setPaid(true);
                $em->persist($paper);
            }

            if (isset($meta['discount'])) {
                $discount = new Discount($this->getUser(), $meta['discount'], $payment);
                $em->persist($discount);
            }


            $em->flush();
        }
        return $this->render('payment_done.html.twig', ['payment' => $payment]);
    }

    private function getReferenceFor($payment)
    {


        $default_params = $this->container->getParameter('bank_params');

        $user = $this->getUser();
        $params = [
            'groupid' => $default_params['groupid'],
            'username' => $default_params['username'],
            'password' => $default_params['password'],
            'bankid' => $default_params['bankid'],
            'callbackurl' => $this->generateUrl('payment_done', ['id' => $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'name' => $user->getFullName(),
            'family' => $user->getFullName(),
            'nc' => $user->getNationalCode(),
            'tel' => $user->getPhoneNumber(),
            'id2' => $default_params['prefix'] . '-' . $payment->getId(),
            'mobile' => $user->getPhoneNumber(),
            'email' => $user->getEmail(),
            'amount' => $payment->calcAmount(),
        ];

        $wsdl = 'https://payment.sharif.ir/research/ws.asmx?wsdl';

        $options = array(
            'uri' => 'http://schemas.xmlsoap.org/soap/envelope/',
            'style' => SOAP_RPC,
            'use' => SOAP_ENCODED,
            'soap_version' => SOAP_1_1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'connection_timeout' => 15,
            'trace' => true,
            'encoding' => 'UTF-8',
            'exceptions' => true,
        );

        $em = $this->getDoctrine()->getManager();
        $soap = new SoapClient($wsdl, $options);
        try {
            $data = $soap->Request($params);
            $result = explode(",", $data->RequestResult);
            if ($result[0] == '0') {
                $payment->setReferenceId($result[1]);
                $em->persist($payment);
                $em->flush();
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    private function checkIfPaymentDone(Payment $payment)
    {
        if ($this->getParameter('payment_debug'))
            return true;
        $default_params = $this->container->getParameter('bank_params');

        $user = $this->getUser();
        $params = [
            'groupid' => $default_params['groupid'],
            'username' => $default_params['username'],
            'password' => $default_params['password'],
            'bankid' => $default_params['bankid'],
            'orderid' => $payment->getReferenceId(),
        ];

        $wsdl = 'https://payment.sharif.ir/research/ws.asmx?wsdl';

        $options = array(
            'uri' => 'http://schemas.xmlsoap.org/soap/envelope/',
            'style' => SOAP_RPC,
            'use' => SOAP_ENCODED,
            'soap_version' => SOAP_1_1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'connection_timeout' => 15,
            'trace' => true,
            'encoding' => 'UTF-8',
            'exceptions' => true,
        );

        $soap = new SoapClient($wsdl, $options);
        try {
            $data = $soap->Status($params);
            $result = explode(":", $data->StatusResult);
            if ($result[0] == '0') {
                return true;
            }
        } catch (\Exception $e) {
        }
        return false;
    }


}
