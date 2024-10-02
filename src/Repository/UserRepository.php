<?php

namespace App\Repository;

use App\Entity\User; // Assurez-vous d'importer la bonne classe
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class); // Utiliser User ici
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) { // Vérifiez que l'utilisateur est une instance de User
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('u') // Changer 'y' en 'u' pour User
            ->andWhere('u.exampleField = :val') // Mettez à jour pour le champ correspondant
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySomeField($value): ?User // Mettre à jour le type de retour
    {
        return $this->createQueryBuilder('u') // Changer 'y' en 'u' pour User
            ->andWhere('u.exampleField = :val') // Mettez à jour pour le champ correspondant
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
