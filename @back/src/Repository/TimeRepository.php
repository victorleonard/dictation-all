<?php

namespace App\Repository;

use App\Entity\Time;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Time>
 *
 * @method Time|null find($id, $lockMode = null, $lockVersion = null)
 * @method Time|null findOneBy(array $criteria, array $orderBy = null)
 * @method Time[]    findAll()
 * @method Time[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Time::class);
    }

    public function findRandomTime()
    {
        $currentUser = $this->security->getUser();

        if (!$currentUser instanceof User) {
            return null; // Aucun utilisateur connecté
        }

        $queryBuilder = $this->createQueryBuilder('w');
        $queryBuilder->leftJoin('w.user', 'u');
        $queryBuilder->andWhere('u.id != :currentUserId OR u.id IS NULL');
        $queryBuilder->setParameter('currentUserId', $currentUser->getId(), UuidType::NAME);

        $times = $queryBuilder->getQuery()->getResult();

        if (!empty($times)) {
            $randomTime = $times[array_rand($times)];
            return $randomTime;
        }

        return null; // Aucun mot disponible pour l'utilisateur actuel
    }

    //    /**
    //     * @return Time[] Returns an array of Time objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Time
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
