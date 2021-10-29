<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\DataGouvClient;

use Netosoft\LocationBundle\DataGouvClient\Model\Feature;
use Netosoft\LocationBundle\DataGouvClient\Model\Geometry;
use Netosoft\LocationBundle\DataGouvClient\Model\Point;
use Netosoft\LocationBundle\DataGouvClient\Model\Properties;
use Netosoft\LocationBundle\ValueObject\AddressObject;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see https://adresse.data.gouv.fr/api-doc/adresse
 */
final class DataGouvClient
{
    public const BASE_URI = 'https://api-adresse.data.gouv.fr/';

    public const TYPE_HOUSENUMBER = 'housenumber';
    public const TYPE_STREET = 'street';
    public const TYPE_LOCALITY = 'locality';
    public const TYPE_MUNICIPALITY = 'municipality';

    private ?HttpClientInterface $client = null;

    public function __construct(private CacheInterface $cache)
    {
    }

    /**
     * @param string $q Utiliser le paramètre q pour faire une recherche plein texte:
     * @param int $limit Avec limit on peut contrôler le nombre d’éléments retournés:
     * @param bool $autocomplete Avec autocomplete on peut désactiver les traitements d’auto-complétion:
     * @param float|null $lat Avec lat et lon on peut donner une priorité géographique:
     * @param null|DataGouvClient::TYPE_* $type         Les filtres type, postcode (code Postal) et citycode (code INSEE) permettent de restreindre la recherche:
     *
     * @return list<Feature>
     *
     * @see https://adresse.data.gouv.fr/api-doc/adresse
     */
    public function search(
        string  $q,
        int     $limit,
        bool    $autocomplete = true,
        ?float  $lat = null,
        ?float  $lon = null,
        ?string $type = null,
        ?string $postcode = null,
        ?string $citycode = null,
    ): array
    {
        $qs = [
            'q' => $q,
            'limit' => $limit,
            'autocomplete' => $autocomplete ? '1' : '0',
        ];

        if (null !== $lat) {
            $qs['lat'] = (string)$lat;
        }

        if (null !== $lon) {
            $qs['lon'] = (string)$lon;
        }

        if (null !== $type) {
            $qs['type'] = $type;
        }

        if (null !== $postcode) {
            $qs['postcode'] = $postcode;
        }

        if (null !== $citycode) {
            $qs['citycode'] = $citycode;
        }

        /** @var array<string, array> $json */
        $json = $this->cache->get(CacheUtils::createCacheKey(\array_merge(['__url__' => '/search/'], $qs)), function (CacheItemInterface $item) use ($qs): array {
            $response = $this->getClient()->request('GET', '/search/', [
                'query' => $qs,
            ]);
            $json = \json_decode($response->getContent(true), true);
            if (!\is_array($json)) {
                throw new DataGouvException('error in api');
            }

            return $json;
        });

        return $this->jsonToFeatureList($json);
    }

    public function geocode(AddressObject $address): ?Point
    {
        $zipcode = \trim($address->getZipcode());
        $street = $address->getStreet();
        if (null !== $street) {
            $q = $street;
        } else {
            $q = $zipcode;
        }

        if ('' === \trim($q)) {
            return null;
        }

        $results = $this->search(q: trim($q . ' ' . $zipcode), limit: 1, postcode: $zipcode);
        $firstResult = \reset($results);

        if (false === $firstResult) {
            return null;
        } else {
            return $firstResult->getGeometry()->getPoint();
        }
    }

    /**
     * @param array<string, mixed> $json
     *
     * @return list<Feature>
     */
    private function jsonToFeatureList(array $json): array
    {
        $res = [];
        /** @psalm-suppress MixedAssignment, MixedArrayAccess, MixedArgument */
        foreach ($json['features'] as $row) {
            $res[] = new Feature(
                geometry: new Geometry(
                    type: $row['geometry']['type'],
                    coordinates: $row['geometry']['coordinates'],
                ),
                properties: new Properties(
                    id: $row['properties']['id'],
                    type: $row['properties']['type'],
                    score: $row['properties']['score'] ?? null,
                    housenumber: $row['properties']['housenumber'] ?? null,
                    name: $row['properties']['name'] ?? null,
                    postcode: $row['properties']['postcode'] ?? null,
                    citycode: $row['properties']['citycode'] ?? null,
                    city: $row['properties']['city'] ?? null,
                    district: $row['properties']['district'] ?? null,
                    oldcity: $row['properties']['oldcity'] ?? null,
                    oldcitycode: $row['properties']['oldcitycode'] ?? null,
                    context: $row['properties']['context'] ?? null,
                    label: $row['properties']['label'] ?? null,
                    x: $row['properties']['x'] ?? null,
                    y: $row['properties']['y'] ?? null,
                    importance: $row['properties']['importance'] ?? null,
                ),
            );
        }

        return $res;
    }

    private function getClient(): HttpClientInterface
    {
        if (null === $this->client) {
            $this->client = HttpClient::createForBaseUri(self::BASE_URI);

            return $this->client;
        }

        return $this->client;
    }
}
