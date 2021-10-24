<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Netosoft\LocationBundle\Entity\Country;

/** @extends ServiceEntityRepository<Country> */
final class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function findOneByCode(string $isoCode): ?Country
    {
        return $this->findOneBy(['isoCode' => $isoCode]);
    }
}
