<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\BookingStatus;
use App\Entity\User;
use App\Entity\Truck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Find bookings by renter
     *
     * @return Booking[]
     */
    public function findByRenter(User $renter): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.renter = :renter')
            ->setParameter('renter', $renter)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find bookings by truck
     *
     * @return Booking[]
     */
    public function findByTruck(Truck $truck): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.truck = :truck')
            ->setParameter('truck', $truck)
            ->orderBy('b.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find active bookings for a truck
     *
     * @return Booking[]
     */
    public function findActiveByTruck(Truck $truck): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.truck = :truck')
            ->andWhere('b.status IN (:statuses)')
            ->setParameter('truck', $truck)
            ->setParameter('statuses', [BookingStatus::CONFIRMED, BookingStatus::IN_PROGRESS])
            ->orderBy('b.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find bookings by status
     *
     * @return Booking[]
     */
    public function findByStatus(BookingStatus $status): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.status = :status')
            ->setParameter('status', $status)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find upcoming bookings
     *
     * @return Booking[]
     */
    public function findUpcoming(int $days = 7): array
    {
        $threshold = new \DateTimeImmutable("+{$days} days");

        return $this->createQueryBuilder('b')
            ->andWhere('b.startDate BETWEEN :now AND :threshold')
            ->andWhere('b.status = :status')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('threshold', $threshold)
            ->setParameter('status', BookingStatus::CONFIRMED)
            ->orderBy('b.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Check if truck is available for date range
     */
    public function isTruckAvailable(Truck $truck, \DateTimeInterface $startDate, \DateTimeInterface $endDate, ?Booking $excludeBooking = null): bool
    {
        $qb = $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.truck = :truck')
            ->andWhere('b.status IN (:statuses)')
            ->andWhere('b.startDate <= :endDate')
            ->andWhere('b.endDate >= :startDate')
            ->setParameter('truck', $truck)
            ->setParameter('statuses', [BookingStatus::CONFIRMED, BookingStatus::PENDING, BookingStatus::IN_PROGRESS])
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        if ($excludeBooking) {
            $qb->andWhere('b.id != :excludeId')
                ->setParameter('excludeId', $excludeBooking->getId());
        }

        return (int) $qb->getQuery()->getSingleScalarResult() === 0;
    }

    /**
     * Find bookings by truck owner
     *
     * @return Booking[]
     */
    public function findByTruckOwner(User $owner): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.truck', 't')
            ->andWhere('t.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
