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
}