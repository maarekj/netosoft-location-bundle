<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\DataGouvClient\Model;

final class Geometry
{
    /**
     * @param "point"                   $type
     * @param array{0: float, 1: float} $coordinates
     */
    public function __construct(
        private string $type,
        private array $coordinates,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCoordinates(): array
    {
        return $this->coordinates;
    }

    public function getLat(): float
    {
        return $this->coordinates[0];
    }

    public function getLon(): float
    {
        return $this->coordinates[1];
    }
}
