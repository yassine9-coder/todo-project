<?php
namespace App\Security;

use App\Service\JwtService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class JwtAuthenticator extends AbstractAuthenticator
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');
        
        if (!$authHeader) {
            throw new AuthenticationException('No authorization header found.');
        }

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            try {
                // Décodage du token
                $decodedToken = $this->jwtService->decodeToken($token);
                
                // Vérification de l'email dans le token
                if (is_array($decodedToken) && isset($decodedToken['email'])) {
                    return new Passport(
                        new UserBadge($decodedToken['email']),
                        new CustomCredentials(
                            function($credentials) use ($decodedToken) {
                                // Validation personnalisée : on vérifie que le token JWT est valide
                                return isset($decodedToken['email']); // Logique de validation
                            },
                            $token
                        )
                    );
                } else {
                    throw new AuthenticationException('Invalid token structure');
                }
            } catch (\Exception $e) {
                throw new AuthenticationException('Invalid JWT token: ' . $e->getMessage());
            }
        }

        throw new AuthenticationException('Authorization header does not contain a valid token.');
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization');
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }
}
