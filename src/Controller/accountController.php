<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountUpdateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class accountController extends AbstractController
{
	/**
     * @Route("/account", name="account")
     */
       public function account(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser(); // Récupérez l'utilisateur connecté (assurez-vous que votre système d'authentification est configuré correctement)
        $form = $this->createForm(AccountUpdateType::class, $user);

        // Gérez la soumission du formulaire ici
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            print_r($user);
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirigez l'utilisateur vers une page de confirmation ou de profil
            return $this->redirectToRoute('account'); 
        }
        
        return $this->render('account.html.twig', [
            'form_account_updated' => $form->createView(),
        ]);
    }
}




