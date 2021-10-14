<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\DataGouvClient\Model;

final class Point
{
    public function __construct(
        private float $lat,
        private float $lng,
    ) {
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLng(): float
    {
        return $this->lng;
    }
}
