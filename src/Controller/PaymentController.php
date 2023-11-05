<?php

namespace App\Controller;

// src/Controller/PaymentController.php

use App\Entity\Inscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Doctrine\ORM\EntityManagerInterface; // Assurez-vous d'importer cette classe
use Symfony\Component\Security\Core\User\UserInterface;


class PaymentController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/payment", name="stripe_payment")
     */
    public function stripePayment(Request $request): Response
    {
        // Configure la clé secrète Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        // Créez un paiement Stripe
        $paymentIntent = PaymentIntent::create([
            'amount' => 20000, 
            'currency' => 'eur',
        ]);


        return $this->render('stripe_payment.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
        ]);

    }
    /**
     * @Route("/payment/success", name="payment_success")
     */
    public function success(Request $request,EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        // Récupérez l'entité User actuellement connectée (vous devrez peut-être ajuster cela en fonction de votre logique d'authentification)
        $user = $this->getUser();

        // Mise à jour du champ "stockage" de l'utilisateur en ajoutant 10
        if ($user) {
            $email = $user->getEmail();
            $user->setStockage($user->getStockage() + 20);
            // Enregistrez la mise à jour dans la base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $inscription = $entityManager->getRepository(Inscription::class)->findOneBy(['userAccountEmail' => $email], ['createdAt' => 'DESC']);

            if($inscription){
                $inscription->setStatus(true);
                $this->entityManager->persist($inscription);
                $this->entityManager->flush();
            }else{
                return $this->redirectToRoute('error');
            }
           
        }

        return $this->render('success_payment.html.twig');
    }
}
