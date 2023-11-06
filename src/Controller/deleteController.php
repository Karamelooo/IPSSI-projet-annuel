<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class deleteController extends AbstractController
{
	/**
     * @Route("/delete", name="app_delete_account")
     */
    public function delete(UserInterface $user, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $tokenStorage = $this->container->get('security.token_storage');
        $tokenStorage->setToken(null);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('homepage');
    }
}
