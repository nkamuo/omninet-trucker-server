<?php

namespace App\Repository;

use App\Entity\TruckDocument;
use App\Entity\Truck;
use App\Entity\DocumentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TruckDocument>
 */
class TruckDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TruckDocument::class);
    }

    /**
     * Find documents by truck
     *
     * @return TruckDocument[]
     */
    public function findByTruck(Truck $truck): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.truck = :truck')
            ->setParameter('truck', $truck)
            ->orderBy('d.uploadedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find documents by truck and type
     *
     * @return TruckDocument[]
     */
    public function findByTruckAndType(Truck $truck, DocumentType $type): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.truck = :truck')
            ->andWhere('d.documentType = :type')
            ->setParameter('truck', $truck)
            ->setParameter('type', $type)
            ->orderBy('d.uploadedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find expired documents
     *
     * @return TruckDocument[]
     */
    public function findExpired(): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.expiryDate IS NOT NULL')
            ->andWhere('d.expiryDate < :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('d.expiryDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find documents expiring soon
     *
     * @return TruckDocument[]
     */
    public function findExpiringSoon(int $days = 30): array
    {
        $threshold = new \DateTime("+{$days} days");

        return $this->createQueryBuilder('d')
            ->andWhere('d.expiryDate IS NOT NULL')
            ->andWhere('d.expiryDate BETWEEN :now AND :threshold')
            ->setParameter('now', new \DateTime())
            ->setParameter('threshold', $threshold)
            ->orderBy('d.expiryDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
