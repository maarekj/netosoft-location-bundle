<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Netosoft\LocationBundle\Entity\Region;
use function Psl\Type\object;
use function Psl\Type\optional;
use function Psl\Type\vec;

/** @extends ServiceEntityRepository<Region> */
final class RegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    /** @return list<string> */
    public function fetchCodesForRegion(Region $region): array
    {
        $codes = [];

        foreach ($region->getCounties() as $county) {
            if (!\in_array($county->getCode(), $codes)) {
                $codes[] = $county->getCode();
            }
        }

        return $codes;
    }

    /** @return list<Region> */
    public function findByCountryCode(string $countryCode): array
    {
        return vec(object(Region::class))
            ->coerce(
                $this->createQueryBuilder('region')
                    ->innerJoin('region.country', 'country')
                    ->andWhere('country.isoCode = :isoCode')
                    ->setParameter('isoCode', $countryCode)
                    ->getQuery()
                    ->getResult()
            );
    }

    public function findOneBySlug(string $countryCode, string $slug): ?Region
    {
        return optional(object(Region::class))
            ->coerce(
                $this->createQueryBuilder('region')
                ->innerJoin('region.country', 'country')
                ->andWhere('country.isoCode = :countryCode')
                ->andWhere('region.slug = :slug')
                ->setMaxResults(1)
                ->setParameter('countryCode', $countryCode)
                ->setParameter('slug', $slug)
                ->getQuery()
                ->getSingleResult()
            );
    }
}
