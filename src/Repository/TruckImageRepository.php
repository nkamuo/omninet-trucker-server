<?php

namespace App\Repository;

use App\Entity\TruckImage;
use App\Entity\Truck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TruckImage>
 */
class TruckImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TruckImage::class);
    }

    /**
     * Find images by truck ordered by display order
     *
     * @return TruckImage[]
     */
    public function findByTruckOrdered(Truck $truck): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.truck = :truck')
            ->setParameter('truck', $truck)
            ->orderBy('i.displayOrder', 'ASC')
            ->addOrderBy('i.uploadedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find primary image for truck
     */
    public function findPrimaryImage(Truck $truck): ?TruckImage
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.truck = :truck')
            ->andWhere('i.isPrimary = :isPrimary')
            ->setParameter('truck', $truck)
            ->setParameter('isPrimary', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
