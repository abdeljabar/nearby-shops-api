<?php

namespace App\Repository;

use App\Entity\Shop;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Shop::class);
    }

//    /**
//     * @return Shop[] Returns an array of Shop objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Shop
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAllWithDistanceOrder($lat, $lng) {

        $qb = $this->createQueryBuilder('s');
        $qb->addSelect('((ACOS(SIN(:lat * PI() / 180) 
            * SIN(s.latitude * PI() / 180) + COS(:lat * PI() / 180) 
            * COS(s.latitude * PI() / 180) 
            * COS((:lng - s.longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) as HIDDEN distance');

        $qb->orderBy('distance');
        $qb->setParameter('lat', $lat);
        $qb->setParameter('lng', $lng);

        return $qb->getQuery()->getResult();
    }

}
