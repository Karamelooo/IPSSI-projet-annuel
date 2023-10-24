<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class subscribeController extends AbstractController
{
	/**
     * @Route("/subscribe", name="subscribe")
     */
    public function subscribe(): Response
    {
        return $this->render('subscribe.html.twig');
    }

    public function subscribe2(): Response
    {
        return $this->render('subscribe2.html.twig');
    }
	// Green Plan
    public function subscribe3(): Response
    {
        return $this->render('subscribe3.html.twig');
    }
	// Paiement
    public function subscribe4(): Response
    {
        return $this->render('subscribe4.html.twig');
    }
}