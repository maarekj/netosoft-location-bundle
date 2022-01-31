<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\ValueObject;

use Netosoft\LocationBundle\Geohash;

final class Coordinate
{
    public function __construct(
        private float $lat,
        private float $lng,
    ) {
    }

    public function copyWith(
        float|null $lat = null,
        float|null $lng = null,
    ): Coordinate {
        return new Coordinate(
            lat: null === $lat ? $this->lat : $lat,
            lng: null === $lng ? $this->lng : $lng
        );
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function withLat(float $lat): Coordinate
    {
        return $this->copyWith(lat: $lat);
    }

    public function getLng(): float
    {
        return $this->lng;
    }

    public function withLng(float $lng): Coordinate
    {
        return $this->copyWith(lng: $lng);
    }

    public function geohash(?int $length = null): string
    {
        return Geohash::encode(coordinate: $this, length: $length);
    }

    public static function fromGeohash(string $geohash): Coordinate
    {
        return Geohash::decodePoint(geohash: $geohash);
    }
}
