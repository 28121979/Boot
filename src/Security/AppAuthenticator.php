<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait; // Utilisation du trait TargetPathTrait

    public const LOGIN_ROUTE = 'app_login'; // Route de connexion

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport // Authentification de l'utilisateur
    {
        $username = $request->request->get('_username'); // Récupération du nom d'utilisateur
        if (null === $username || '' === $username) {
            throw new AuthenticationException('Nom d\'utilisateur non fourni.');
        }

        $password = $request->request->get('_password'); // Récupération du mot de passe
        if (null === $password || '' === $password) {
            throw new AuthenticationException('Mot de passe non fourni.');
        }

        $csrfToken = $request->request->get('_csrf_token'); // Récupération du jeton CSRF
        if (null === $csrfToken || '' === $csrfToken) {
            throw new AuthenticationException('Jeton CSRF manquant.');
        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username); // Stockage du nom d'utilisateur en session

        return new Passport( // Création du passeport
            new UserBadge($username), // Utilisation du nom d'utilisateur
            new PasswordCredentials($password), // Utilisation du mot de passe
            [
                new CsrfTokenBadge('authenticate', $csrfToken), // Utilisation du jeton CSRF
                new RememberMeBadge(), // Utilisation du badge RememberMe
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response // Redirection après connexion
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) { 
            return new RedirectResponse($targetPath); // Redirection vers la page précédente
        }

        $roles = $token->getUser()->getRoles(); // Récupération des rôles de l'utilisateur
        $redirectRoute = $this->getRedirectRouteBasedOnRole($roles); // Récupération de la route de redirection

        if ($redirectRoute) { // Redirection en fonction du rôle de l'utilisateur
            return new RedirectResponse($this->urlGenerator->generate($redirectRoute)); // Redirection vers la route correspondante
        } else {
            throw new \Exception('Pas de route trouvée pour le rôle de l\'utilisateur'); // Exception si aucun rôle n'est trouvé
        }
    }
    private function getRedirectRouteBasedOnRole(array $roles): ?string // Gestion des rôles utilisateur pour la redirection après connexion
    {
        if (in_array('ROLE_ADMIN', $roles, true)) { // Si l'utilisateur a le rôle ROLE_ADMIN
            return 'admin';
        } elseif (in_array('ROLE_USER', $roles, true)) { // Si l'utilisateur a le rôle ROLE_USER
            return 'app_profile';
        }

        return null; // Retourne null si aucun rôle n'est trouvé
    }
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE); // Retourne la route de connexion
    }
}
