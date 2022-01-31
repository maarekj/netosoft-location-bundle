<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\ValueObject;

final class BoundingBox
{
    public function __construct(
        private Coordinate $sw,
        private Coordinate $ne,
    ) {
    }

    public function copyWith(
        Coordinate|null $sw = null,
        Coordinate|null $ne = null,
    ): BoundingBox {
        return new BoundingBox(
            sw: null === $sw ? $this->sw : $sw,
            ne: null === $ne ? $this->ne : $ne,
        );
    }

    public function getSw(): Coordinate
    {
        return $this->sw;
    }

    public function withSw(Coordinate $sw): BoundingBox
    {
        return $this->copyWith(sw: $sw);
    }

    public function getNe(): Coordinate
    {
        return $this->ne;
    }

    public function withNe(Coordinate $ne): BoundingBox
    {
        return $this->copyWith(ne: $ne);
    }
}
