<?php

namespace App\Repository;

use App\Entity\Truck;
use App\Entity\TruckStatus;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Truck>
 */
class TruckRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Truck::class);
    }

    /**
     * Find available trucks
     *
     * @return Truck[]
     */
    public function findAvailable(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', TruckStatus::AVAILABLE)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find trucks by owner
     *
     * @return Truck[]
     */
    public function findByOwner(User $owner): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find available trucks for date range
     *
     * @return Truck[]
     */
    public function findAvailableForDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.bookings', 'b')
            ->andWhere('t.status = :status')
            ->andWhere(
                '(b.id IS NULL) OR ' .
                '(b.status NOT IN (:bookingStatuses)) OR ' .
                '(b.endDate < :startDate OR b.startDate > :endDate)'
            )
            ->setParameter('status', TruckStatus::AVAILABLE)
            ->setParameter('bookingStatuses', ['confirmed', 'pending', 'in_progress'])
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search trucks by criteria
     *
     * @param array<string, mixed> $criteria
     * @return Truck[]
     */
    public function search(array $criteria): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', TruckStatus::AVAILABLE);

        if (isset($criteria['truckType'])) {
            $qb->andWhere('t.truckType = :truckType')
                ->setParameter('truckType', $criteria['truckType']);
        }

        if (isset($criteria['maxDailyRate'])) {
            $qb->andWhere('t.dailyRate <= :maxDailyRate')
                ->setParameter('maxDailyRate', $criteria['maxDailyRate']);
        }

        if (isset($criteria['minPayload'])) {
            $qb->andWhere('t.maxPayload >= :minPayload')
                ->setParameter('minPayload', $criteria['minPayload']);
        }

        if (isset($criteria['location'])) {
            $qb->andWhere('t.location LIKE :location')
                ->setParameter('location', '%' . $criteria['location'] . '%');
        }

        return $qb->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
