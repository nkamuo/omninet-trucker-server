<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    /**
     * Find active companies
     *
     * @return Company[]
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', \App\Entity\CompanyStatus::ACTIVE)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find company by DOT number
     */
    public function findByDotNumber(string $dotNumber): ?Company
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.dotNumber = :dotNumber')
            ->setParameter('dotNumber', $dotNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
