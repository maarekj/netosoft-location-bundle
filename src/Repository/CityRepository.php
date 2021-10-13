<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Netosoft\LocationBundle\Entity\City;

/** @extends ServiceEntityRepository<City> */
final class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function findOneByZipcode(string $zipcode, string $country): ?City
    {
        $qb = $this->createQueryBuilder('city');
        $qb->innerJoin('city.county', 'county');
        $qb->innerJoin('county.region', 'region');
        $qb->innerJoin('region.country', 'country');
        $qb->andWhere('
        city.zipcode LIKE :zipcode1
        OR city.zipcode LIKE :zipcode2
        OR city.zipcode LIKE :zipcode3
        OR city.zipcode LIKE :zipcode4
        ');
        $qb->andWhere('country.isoCode = :country');

        $qb->setParameter('zipcode1', $zipcode);
        $qb->setParameter('zipcode2', $zipcode.'-%');
        $qb->setParameter('zipcode3', '%-'.$zipcode);
        $qb->setParameter('zipcode4', '%-'.$zipcode.'-%');
        $qb->setParameter('country', $country);
        $qb->setMaxResults(1);

        /** @var list<City> $results */
        $results = $qb->getQuery()->getResult();
        $result = \reset($results);

        return false === $result ? null : $result;
    }
}
