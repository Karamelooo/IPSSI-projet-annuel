<?php

namespace App\Controller;

use App\Entity\UploadedFile;
use App\Form\UploadedFileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadedFileController extends AbstractController
{
    #[Route('/uploaded', name: 'app_uploaded')]
    public function index(Request $request): Response
    {
        // Crée une nouvelle entité UploadedFile
        $uploadedFile = new UploadedFile();

        // Crée le formulaire en utilisant la classe UploadedFileType et liez-le à l'entité $uploadedFile
        $form = $this->createForm(UploadedFileType::class, $uploadedFile);

        // Gérez la requête HTTP
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ici, vous traiteriez le fichier téléchargé comme nécessaire

            // Enregistrez l'entité $uploadedFile avec Doctrine
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($uploadedFile);
            $entityManager->flush();

            // Ajoutez un message flash de succès
            $this->addFlash('success', 'File uploaded successfully!');

            // Redirigez vers une autre page, par exemple la liste des fichiers
            return $this->redirectToRoute('app_files_list');
        }

        // Renvoie la vue du formulaire si elle n'a pas été soumise ou si elle n'est pas valide
        return $this->render('uploaded_file/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
