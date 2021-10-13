<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Netosoft\LocationBundle\Entity\County;
use Psl\Type;

/** @extends ServiceEntityRepository<County> */
final class CountyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, County::class);
    }

    /** @return list<County> */
    public function findByCountryCode(string $countryCode): array
    {
        $qb = $this->createQueryBuilder('county');
        $qb->innerJoin('county.region', 'region')
            ->innerJoin('region.country', 'country')
            ->andWhere('country.isoCode = :isoCode')
            ->setParameter('isoCode', $countryCode);

        return Type\vec(Type\object(County::class))
            ->coerce($qb->getQuery()->getResult());
    }

    public function findOneByCode(string $code, ?string $countryCode = null): ?County
    {
        if (null === $countryCode) {
            return $this->findOneBy(['code' => $code]);
        }

        $qb = $this->createQueryBuilder('county');
        $qb->innerJoin('county.region', 'region');
        $qb->innerJoin('region.country', 'country');
        $qb->andWhere('country.isoCode = :countryCode');
        $qb->andWhere('county.code = :code');
        $qb->setParameter('countryCode', $countryCode);
        $qb->setParameter('code', $code);

        return Type\optional(Type\object(County::class))
            ->coerce($qb->getQuery()->getSingleResult());
    }
}
