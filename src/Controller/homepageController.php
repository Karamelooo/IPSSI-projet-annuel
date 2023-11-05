<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class homepageController extends AbstractController
{
	/**
     * @Route("/", name="homepage")
     */
    public function homepage(Security $security): RedirectResponse
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('folder');
        }
        return $this->redirectToRoute('app_login'); 
    
    }
}