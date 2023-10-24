<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class accountController extends AbstractController
{
	/**
     * @Route("/account", name="account")
     */
    public function account(): Response
    {
        return $this->render('account.html.twig');
    }
}