<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Form\InscriptionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class InscriptionController extends AbstractController
{
    public function etape1(Request $request,EntityManagerInterface $entityManager): Response
    {
        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription);
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $orderNumber = random_int(100000, 999999);

            if ($form->isSubmitted() && $form->isValid()) {
                $formData = [
                    'firstname' => $form->get('firstname')->getData(),
                    'lastname' => $form->get('lastname')->getData(),
                    'email' => $form->get('email')->getData(),
                    'addressUser'=> $form->get('addressUser')->getData(),
                    'addressEntreprise'=> $form->get('addressEntreprise')->getData(),
                    'siret'=> $form->get('siret')->getData()
                ];
                $inscription->setStatus(false);
                $inscription->setUserAccountEmail($user->getEmail());
                $inscription->setCreatedAt(new \DateTimeImmutable());
                $inscription->setOrderNumber($orderNumber);
                $inscription->setFirstname($formData['firstname']);
                $inscription->setLastname($formData['lastname']);
                $inscription->setEmail($formData['email']);
                $inscription->setAddressUser($formData['addressUser']);
                $inscription->setAddressEntreprise($formData['addressEntreprise']);
                $inscription->setSiret($formData['siret']);
                
                $entityManager->persist($inscription);
                $entityManager->flush();

                return $this->redirectToRoute('stripe_payment');
            }
        }

        return $this->render('inscription_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Ajoutez d'autres actions pour les étapes suivantes si nécessaire
}
