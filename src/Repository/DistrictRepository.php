<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Netosoft\LocationBundle\Entity\District;
use function Psl\Type\object;
use function Psl\Type\optional;

/** @extends ServiceEntityRepository<District> */
final class DistrictRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, District::class);
    }

    public function findOneByZipcode(string $zipcode, string $country): ?District
    {
        $qb = $this->createQueryBuilder('district');
        $qb->innerJoin('district.city', 'city');
        $qb->innerJoin('city.county', 'county');
        $qb->innerJoin('county.region', 'region');
        $qb->innerJoin('region.country', 'country');
        $qb->andWhere('
        district.zipcode LIKE :zipcode1
        OR district.zipcode LIKE :zipcode2
        OR district.zipcode LIKE :zipcode3
        OR district.zipcode LIKE :zipcode4
        ');
        $qb->andWhere('country.isoCode = :country');

        $qb->setParameter('zipcode1', $zipcode);
        $qb->setParameter('zipcode2', $zipcode.'-%');
        $qb->setParameter('zipcode3', '%-'.$zipcode);
        $qb->setParameter('zipcode4', '%-'.$zipcode.'-%');
        $qb->setParameter('country', $country);
        $qb->setMaxResults(1);

        return optional(object(District::class))
            ->coerce($qb->getQuery()->getSingleResult());
    }
}
