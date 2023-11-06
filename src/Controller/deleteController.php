<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UploadedFileRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class deleteController extends AbstractController
{
	/**
     * @Route("/delete", name="app_delete_account")
     */
    public function delete(UserInterface $user, EntityManagerInterface $entityManager, UploadedFileRepository $UploadedFileRepository): Response
    {
        $user = $this->getUser();
        $tokenStorage = $this->container->get('security.token_storage');
        $userFiles = $UploadedFileRepository->findBy(['user' => $user]);
        $fileSystem = new Filesystem();
        $targetDirectory = $this->getParameter('uploads_directory');
        
        foreach ($userFiles as $file) {
            {
                try {
                    $fileSystem->remove($targetDirectory . '/' . $file->getFile());
                } catch (IOExceptionInterface $exception) {
                    echo "Erreur lors de la suppression du fichier dans " . $exception->getPath();
                }
            }
        }
        // Supprimez les fichiers de la base de données
        foreach ($userFiles as $file) {
            $entityManager->remove($file);
        }

        $tokenStorage->setToken(null);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('homepage');
    }
}
