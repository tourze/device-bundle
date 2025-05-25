<?php

namespace DeviceBundle\Repository;

use DeviceBundle\Entity\LoginLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method LoginLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginLog[]    findAll()
 * @method LoginLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginLog::class);
    }

    public function findLastByUser(UserInterface $user): ?LoginLog
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :user')
            ->setParameter('user', $user)
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
