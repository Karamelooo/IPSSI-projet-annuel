<?php

// src/Controller/InvoiceController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PdfGeneratorService;
use App\Entity\Inscription;
use Doctrine\ORM\EntityManagerInterface; // Importez EntityManagerInterface
use App\Entity\User;

class InvoiceController extends AbstractController
{
    private $pdfGeneratorService;
    private $entityManager;

    public function __construct(PdfGeneratorService $pdfGeneratorService, EntityManagerInterface $entityManager)
    {
        $this->pdfGeneratorService = $pdfGeneratorService;
        $this->entityManager = $entityManager;
    }

    #[Route('/generate-invoice-pdf/{orderNumber}', name: 'generate_invoice_pdf')] // Utilisez inscriptionId au lieu de invoiceId
    public function generateInvoicePdf($orderNumber): Response // Utilisez inscriptionId au lieu de invoiceId
    {
        // Récupérez l'utilisateur connecté
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createNotFoundException('Utilisateur non connecté');
        }

        $userEmail = $user->getEmail();

        // Récupérez les données de facture en fonction de l'inscriptionOrderNumber et de l'utilisateur connecté
        $inscription = $this->entityManager->getRepository(Inscription::class)->findOneBy([
            'orderNumber' => $orderNumber,
            'userAccountEmail' => $userEmail,
        ]);

        if (!$inscription) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Générez le contenu HTML de la facture (vous devrez créer votre propre modèle Twig)
        $htmlContent = $this->renderView('invoice/invoice.html.twig', ['inscription' => $inscription]);

        // Générez le PDF
        $pdfContent = $this->pdfGeneratorService->generatePdf($htmlContent);

        // Créez une réponse HTTP avec le contenu PDF
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="facture.pdf"');

        return $response;
    }
}
