<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AppCustomAuthenticator extends AbstractAuthenticator
{

    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): ?bool
    {
        // Déterminez si ce custom authenticator prend en charge la requête.
        // Exemple : vérifiez si la requête contient un paramètre d'authentification.
        return $request->query->has('custom_token');
    }

    public function authenticate(Request $request): Passport
    {
        // Créez et renvoyez un objet Passport qui contient les informations d'authentification.
        // Exemple : extrayez le token d'authentification de la requête.
        $customToken = $request->query->get('custom_token');
        
        // Créez un objet UserBadge ou votre propre badge personnalisé.
        $userBadge = new UserBadge($customToken);

        // Créez un objet PasswordCredentials et fournissez le mot de passe si nécessaire.
        $passwordCredentials = new PasswordCredentials(['custom_token' => $customToken]);

        // Créez le Passport et ajoutez les badges nécessaires.
        $passport = new Passport($userBadge, $passwordCredentials);

        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Gérez les actions à effectuer lorsque l'authentification est réussie, par exemple, rediriger l'utilisateur vers une page spécifique.
        // Exemple : redirigez l'utilisateur vers son tableau de bord.
        return new RedirectResponse($this->urlGenerator->generate('account'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Gérez les actions à effectuer lorsque l'authentification échoue, par exemple, affichez un message d'erreur.
        // Exemple : affichez un message d'erreur personnalisé.
        $errorMessage = 'L\'authentification a échoué : ' . $exception->getMessage();
        return new RedirectResponse($this->urlGenerator->generate('login'));
    }
}

