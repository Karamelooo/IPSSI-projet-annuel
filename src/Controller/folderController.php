<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class folderController extends AbstractController
{
	/**
     * @Route("/folder", name="folder")
     */
    public function folder(): Response
    {
        return $this->render('folder.html.twig');
    }
}