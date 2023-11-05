<?php

namespace App\Controller;

use DateTime;
use SplFileInfo;
use App\Entity\UploadFile;
use App\Form\UploadedFileType;
use App\Repository\UploadedFileRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\BinaryFileResponse;




class UploadedFileController extends AbstractController
{
    //const BYTES_IN_ONE_GIGABYTE = 1.0e9; // 1 Go = 1 x 10^9 octets
    const BYTES_IN_ONE_GIGABYTE = 1024 * 1024 * 1024;
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(Request $request, EntityManagerInterface $entityManager, UploadedFileRepository $UploadedFileRepository, UserRepository $UserRepository): Response
    {
        $file = new UploadFile();
        $user = $this->getUser();
    
        $form = $this->createForm(UploadedFileType::class, $file);
        $form->handleRequest($request);

        $sortColumn = $request->query->get('sort', 'date'); // Par défaut trié par date
        $sortDirection = $request->query->get('direction', 'DESC'); // Par défaut en ordre décroissant
        
        // Utiliser les paramètres de tri lors de la récupération des fichiers
       
        $totalFilesCount = 0;
        $filesTodayCount = 0;
        $averageFilesPerUser = 0;
        // Vérifier si l'utilisateur a le rôle admin
        if ($this->isGranted('ROLE_ADMIN')) {
            // Initialiser les variables pour les informations demandées
            
            
            // Récupérer tous les fichiers si l'utilisateur est admin
            $allFiles = $UploadedFileRepository->findAll();

            // Obtenir le nombre total de fichiers
            $totalFilesCount = count($allFiles);

            // Obtenir le nombre de fichiers uploadés aujourd'hui
            $filesTodayCount = $UploadedFileRepository->countFilesUploadedToday();

            // Obtenir le nombre total d'utilisateurs
            $totalUsersCount = $UserRepository->count([]);

            // Calculer le nombre moyen de fichiers par utilisateur
            if ($totalUsersCount > 0) {
                $averageFilesPerUser = $totalFilesCount / $totalUsersCount;
            }
        } else {
            // Récupérer seulement les fichiers de l'utilisateur courant
            $allFiles = $UploadedFileRepository->findBy(['user' => $user]);
        }
        $orderBy = [$sortColumn => $sortDirection];

        $allFiles = $UploadedFileRepository->findByCriteria([], $orderBy);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['file']->getData();
        
            // Get the original file name
            $originalFileName = $uploadedFile->getClientOriginalName();
        
            // Calculate the file size in Gigabytes (GB)
            $fileSizeInGigabytes = $uploadedFile->getSize() / self::BYTES_IN_ONE_GIGABYTE;
        
            // Check if the user has exceeded their storage limit
            if (($user->getStorageUse() + $fileSizeInGigabytes) > $user->getStockage()) {
                $this->addFlash('error', 'Vous avez dépassé votre limite de stockage.');
                return $this->redirectToRoute('app_dashboard');
            }
        
            // Update the user's used storage in Gigabytes
            $user->setStorageUse($user->getStorageUse() + $fileSizeInGigabytes);
            $entityManager->persist($user);
        
    
            $targetDirectory = $this->getParameter('uploads_directory');
            $fileName = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($targetDirectory, $fileName);
    
            $movedFile = new SplFileInfo($this->getParameter('uploads_directory').'/'.$fileName);
    
            $file->setName($originalFileName);
            $file->setSize($movedFile->getSize());
            $file->setOwner($user->getEmail());
            $file->setDate(new DateTime());
            $file->setLastAction(new DateTime());
            $file->setFile($fileName);
            $file->setUser($this->getUser());
    
            try {
                $entityManager->persist($file);
                $entityManager->flush();
                $this->addFlash('success', 'Votre fichier a été téléchargé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors du téléchargement du fichier.');
            }
    
            return $this->redirectToRoute('app_dashboard');
        }
    
        return $this->render('dashboard/index.html.twig', [
            'form' => $form->createView(),
            'files' => $allFiles,
            'totalFilesCount' => $totalFilesCount,
            'filesTodayCount' => $filesTodayCount,
            'averageFilesPerUser' => $averageFilesPerUser,
        ]);
    }
    
    #[Route('/dashboard/delete/{id}', name: 'dashboard_delete', methods: ['POST'])]
    public function delete(int $id, UploadedFileRepository $UploadedFileRepository, EntityManagerInterface $entityManager): Response
    {
        $file = $UploadedFileRepository->find($id);
        $user = $this->getUser();
    
        if (!$file || $file->getUser() !== $user) {
            throw $this->createNotFoundException('Fichier introuvable ou vous n’avez pas le droit d’accéder à ce fichier.');
        }
    
        $fileSizeToRemove = $file->getSize();
    
        $updatedStorageUse = max($user->getStorageUse() - $fileSizeToRemove, 0);
        $user->setStorageUse($updatedStorageUse);
        $entityManager->persist($user);
    
        $fileSystem = new Filesystem();
        $targetDirectory = $this->getParameter('uploads_directory');
        try {
            $fileSystem->remove($targetDirectory . '/' . $file->getFile());
        } catch (IOExceptionInterface $exception) {
            echo "Erreur lors de la suppression du fichier dans " . $exception->getPath();
        }
    
        $entityManager->remove($file);
        $entityManager->flush();
    
        $this->addFlash('success', 'Fichier supprimé avec succès.');
        return $this->redirectToRoute('app_dashboard');
    }



#[Route('/dashboard/download/{id}', name: 'dashboard_download')]
public function download(int $id, UploadedFileRepository $UploadedFileRepository): Response
{
    $fileEntity = $UploadedFileRepository->find($id);
    if(!$fileEntity || $fileEntity->getUser() !== $this->getUser()) {
        throw $this->createNotFoundException('Fichier non trouvé ou accès non autorisé.');
    }

    $filePath = $this->getParameter('uploads_directory').'/'.$fileEntity->getFile();
    
    return $this->file($filePath, $fileEntity->getName());
}





#[Route('/dashboard/{filename}', name: 'app_dashboard_serve')]
public function serveFile(string $filename): Response
{
    $file = $this->getParameter('uploads_directory').'/'.$filename;

    if (!file_exists($file)) {
        throw $this->createNotFoundException('File not found.');
    }

    $response = new BinaryFileResponse($file);
    $response->headers->set('Content-Type', mime_content_type($file));
    $response->headers->set('Content-Disposition', 'inline; filename="'.basename($file).'"');

    return $response;
}






#[Route('/dashboard/view/{id}', name: 'app_dashboard_view')]
public function viewFile($id, UploadedFileRepository $UploadedFileRepository): Response
{
    $file = $UploadedFileRepository->find($id);
    
    if (!$file || $file->getUser() !== $this->getUser()) {
        throw $this->createNotFoundException('File not found or you do not have permission to view it.');
    }

    $fileExtension = pathinfo($file->getFile(), PATHINFO_EXTENSION);

    $isPdf = in_array($fileExtension, ['pdf']);
    $isImage = in_array($fileExtension, ['png', 'jpeg', 'jpg', 'gif', 'bmp', 'webp', 'svg', 'tiff']);
    $isVideo = in_array($fileExtension, ['mp4', 'avi', 'mov']);
    $isAudio = in_array($fileExtension, ['mp3', 'wav']);
    $isDocument = in_array($fileExtension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt']);
    

    return $this->render('dashboard/view.html.twig', [
        'file' => $file,
        'isPdf' => $isPdf,
        'isImage' => $isImage,
        'isVideo' => $isVideo,
        'isAudio' => $isAudio,
        'isDocument' => $isDocument,
    ]);
}
}
