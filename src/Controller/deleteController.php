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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class deleteController extends AbstractController
{
	/**
     * @Route("/delete", name="app_delete_account")
     */
    public function delete(UserInterface $user, EntityManagerInterface $entityManager, UploadedFileRepository $UploadedFileRepository, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        $mail = $user->getEmail();
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

        // notification à l'user que son compte a été supprimé
        $email = (new Email())
        ->from($_ENV['MAILER_ADDRESS'])
        ->to($mail)
        ->subject('Votre compte a été supprimé')
        ->text('Votre compte lié au mail '.$mail.' a bien été supprimé')
        ->html('<p>Votre compte lié au mail '.$mail.' a bien été supprimé</p>');
    
        $mailer->send($email);

        // notification à l'admin qu'un compte a été supprimé
        $email = (new Email())
        ->from($_ENV['MAILER_ADDRESS'])
        ->to($_ENV['MAILER_ADDRESS'])
        ->subject('Le compte '.$mail.' a été supprimé')
        ->text('Le compte lié au mail '.$mail.' a été supprimé')
        ->html('<p>Le compte lié au mail '.$mail.' a été supprimé</p>');

        $mailer->send($email);

        $tokenStorage->setToken(null);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('homepage');
    }
}
