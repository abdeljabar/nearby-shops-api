<?php

namespace App\Repository;

use App\Entity\Shop;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findPreferred($userId) {
        $qb = $this->createQueryBuilder('s');
        $qb->join('s.likers u', 'WITH u.id=:userId');
        $qb->setParameter('userId', $userId);

        return $qb->getQuery()->getResult();
    }

    public function findAllWithDistanceOrder($lat, $lng) {

        $qb = $this->createQueryBuilder('s');
        $this->withDistanceOrder($qb, $lat, $lng);

        return $qb->getQuery()->getResult();
    }

    public function findNonDisliked($userId) {

        $date = new \DateTime();
        $date->modify('-2 hour');

        $qb = $this->createQueryBuilder('s');

        $this->whereLikedShopsAreExcluded($qb);
        $this->whereDislikedShopsAreExcluded($qb, $date);

        $qb->setParameter('userId', $userId);

        return $qb->getQuery()->getResult();
    }

    public function findNonDislikedWithDistanceOrder($lat, $lng, $userId) {

        $date = new \DateTime();
        $date->modify('-2 hour');

        $qb = $this->createQueryBuilder('s');
        $this->withDistanceOrder($qb, $lat, $lng);

        $this->whereLikedShopsAreExcluded($qb);
        $this->whereDislikedShopsAreExcluded($qb, $date);

        $qb->setParameter('userId', $userId);

        return $qb->getQuery()->getResult();
    }

    private function withDistanceOrder(QueryBuilder $qb, $lat, $lng) {
        $qb->addSelect('((ACOS(SIN(:lat * PI() / 180) 
            * SIN(s.latitude * PI() / 180) + COS(:lat * PI() / 180) 
            * COS(s.latitude * PI() / 180) 
            * COS((:lng - s.longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) as HIDDEN distance');

        $qb->orderBy('distance');

        $qb->setParameter('lat', $lat);
        $qb->setParameter('lng', $lng);
    }

    private function whereDislikedShopsAreExcluded(QueryBuilder $qb, $date) {
        $qb->andWhere('s.id not in (select IDENTITY(ds.shop) FROM App:DislikedShop ds where IDENTITY(ds.user)=:userId AND ds.updatedAt > :date)');
        $qb->setParameter('date', $date);
    }

    private function whereLikedShopsAreExcluded(QueryBuilder $qb) {
        $qb->andWhere('s.id not in (select ss.id FROM App:Shop ss JOIN ss.likers sl where sl.id=:userId)');
    }

}
