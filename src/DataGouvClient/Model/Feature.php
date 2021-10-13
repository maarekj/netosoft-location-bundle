<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\DataGouvClient\Model;

final class Feature
{
    public function __construct(
        private Geometry $geometry,
        private Properties $properties,
    ) {
    }

    public function getGeometry(): Geometry
    {
        return $this->geometry;
    }

    public function getProperties(): Properties
    {
        return $this->properties;
    }
}
