<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\User;
use App\Form\AccountUpdateType;
use App\Form\AccountUpdate2Type;
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
        $form2 = $this->createForm(AccountUpdate2Type::class, $user);
        $user_data = $entityManager->getRepository(User::class)->find($user);


         // Récupérez les dernières commandes de l'utilisateur
         $orders = $entityManager->getRepository(Inscription::class)
         ->findBy(['userAccountEmail' => $user->getEmail()], ['createdAt' => 'DESC']);

        // Gérez la soumission du formulaire ici
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = [
                'prenom' => $form->get('prenom')->getData(),
                'nom' => $form->get('nom')->getData(),
                'adresse'=> $form->get('adresse')->getData()
            ];
            $user_data->setPrenom($formData['prenom']);
            $user_data->setNom($formData['nom']);
            $user_data->setAdresse($formData['adresse']);

            $entityManager->persist($user_data);
            $entityManager->flush();

            // Redirigez l'utilisateur vers une page de confirmation ou de profil
            return $this->redirectToRoute('account'); 
        }
        
        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->isValid()) {
            $formData = [
                'company_address'=> $form2->get('company_address')->getData(),
                'company_siret'=> $form2->get('company_siret')->getData()
            ];
            $user_data->setCompanyAddress($formData['company_address']);
            $user_data->setCompanySiret($formData['company_siret']);

            $entityManager->persist($user_data);
            $entityManager->flush();

            // Redirigez l'utilisateur vers une page de confirmation ou de profil
            return $this->redirectToRoute('account'); 
        }
        
        return $this->render('account.html.twig', [
            'form_account_updated' => $form->createView(),
            'form_account_updated2' => $form2->createView(),
            'orders' => $orders, 

        ]);
    }
    
}




