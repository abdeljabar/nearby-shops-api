<?php

namespace App\Repository;

use App\Entity\DislikedShop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DislikedShop|null find($id, $lockMode = null, $lockVersion = null)
 * @method DislikedShop|null findOneBy(array $criteria, array $orderBy = null)
 * @method DislikedShop[]    findAll()
 * @method DislikedShop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DislikedShopRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DislikedShop::class);
    }

//    /**
//     * @return DislikedShop[] Returns an array of DislikedShop objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DislikedShop
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
