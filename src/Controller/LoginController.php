<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\JwtService;
use Psr\Log\LoggerInterface;

class LoginController extends AbstractController
{
    private JwtService $jwtService;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(JwtService $jwtService, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->jwtService = $jwtService;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/api/login", methods={"POST"})
     */
    public function login(Request $request): JsonResponse
    {
        // Decode JSON request body
        $data = json_decode($request->getContent(), true);

        // Check if email and password are present
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Find user by email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        // Check if user exists
        if (!$user) {
            $this->logger->warning('Login attempt with invalid email: ' . $data['email']);
            return new JsonResponse(['error' => 'Invalid credentials.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

     

        // Generate JWT token using JwtService
        try {
            // Ensure user entity or required details are properly passed to the JwtService
            if (!$user->getId() || !$user->getEmail()) {
                throw new \InvalidArgumentException('User data is incomplete for token generation.');
            }

            $token = $this->jwtService->generateToken($user);
        } catch (\InvalidArgumentException $e) {
            $this->logger->error('Invalid argument during token generation: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Token generation failed due to invalid data.'], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error during token generation: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Token generation failed. Please try again later.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Return user details and JWT token
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            // Add any other user fields you want to expose
        ];

        return new JsonResponse([
            'message' => 'Login successful',
            'user' => $userData,
            'token' => $token
        ]);
    }
     /**
     * @Route("/api/logout", methods={"POST"})
     */
    public function logout(Request $request): JsonResponse
    {
        // Get the token from the Authorization header
        $authorizationHeader = $request->headers->get('Authorization');
        if ($authorizationHeader) {
            list(, $token) = explode(' ', $authorizationHeader);

            // Invalidate the token (optional, if you are using a blacklist)
            $this->tokenBlacklistService->invalidateToken($token);

            return new JsonResponse(['message' => 'Logout successful.'], JsonResponse::HTTP_OK);
        }

        return new JsonResponse(['error' => 'Token is required for logout.'], JsonResponse::HTTP_BAD_REQUEST);
    }
}
