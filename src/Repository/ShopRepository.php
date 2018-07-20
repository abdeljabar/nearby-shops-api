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

    public function findNonDisliked() {

        $date = new \DateTime();
        $date->modify('-2 hour');

        $qb = $this->createQueryBuilder('s');

        $qb->leftJoin('s.dislikedShops', 'ds');
        $qb->where('ds.id is null');
        $qb->orWhere('ds.updatedAt < :date');
        $qb->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    public function findNonDislikedWithDistanceOrder($lat, $lng) {

        $date = new \DateTime();
        $date->modify('-2 hour');

        $qb = $this->createQueryBuilder('s');
        $qb->addSelect('((ACOS(SIN(:lat * PI() / 180) 
            * SIN(s.latitude * PI() / 180) + COS(:lat * PI() / 180) 
            * COS(s.latitude * PI() / 180) 
            * COS((:lng - s.longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) as HIDDEN distance');

        $qb->leftJoin('s.dislikedShops', 'ds');
        $qb->where('ds.id is null');
        $qb->orWhere('ds.updatedAt < :date');
        $qb->orderBy('distance');
        $qb->setParameter('lat', $lat);
        $qb->setParameter('lng', $lng);
        $qb->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    public function findPreferred($userId) {
        $qb = $this->createQueryBuilder('s');
        $qb->join('s.users u', 'WITH u.id=:user');
        $qb->setParameter('user', $userId);

        return $qb->getQuery()->getResult();
    }

}
