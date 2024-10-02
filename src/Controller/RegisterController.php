<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Remplace ici
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    private $entityManager;
    private $passwordHasher; // Renomme ici
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher; // Renomme ici
        $this->validator = $validator;
    }

    /**
     * @Route("/api/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (empty($email) || empty($password)) {
            return new JsonResponse(['message' => 'Missing email or password'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setemail($email);

        // Hash the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password); // Utilise la méthode hashPassword
        $user->setPassword($hashedPassword);

        // Validate the user entity
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorMessage = (string) $errors;
            return new JsonResponse(['message' => $errorMessage], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Save user to database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully'], JsonResponse::HTTP_CREATED);
    }
}
