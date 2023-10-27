<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Form\LoginFormType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    private $entityManager;
    private $security;
    
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(LoginFormType::class);
        $form->handleRequest($request);

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        print_r("de");
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérez les données du formulaire de connexion
            $data = $form->getData();

            // Ici, vous pouvez vérifier les informations de connexion par rapport à votre base de données
            // Par exemple, comparez le nom d'utilisateur et le mot de passe avec les données enregistrées dans votre base de données
            // Si l'authentification réussit, créez un token et connectez l'utilisateur

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);
            print_r("dede",$user);
            
            
        }

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
