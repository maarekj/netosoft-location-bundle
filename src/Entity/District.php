<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Netosoft\LocationBundle\Repository\DistrictRepository;

#[Entity(repositoryClass: DistrictRepository::class)]
#[Table(name: 'location__district')]
#[Index(columns: ['slug'], name: 'idx_location__district__slug')]
#[Index(columns: ['zipcode'], name: 'idx_location__district__zipcode')]
#[Index(columns: ['name'], name: 'idx_location__district__name')]
#[Index(columns: ['zipcode', 'name'], name: 'idx_location__district__zipcode_name')]
class District implements \Stringable
{
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[ManyToOne(targetEntity: City::class, inversedBy: 'districts')]
    private City $city;

    #[Column(name: 'name', type: 'string', length: 255)]
    private string $name;

    #[Column(name: 'slug', type: 'string', length: 255)]
    private string $slug;

    #[Column(name: 'zipcode', type: 'string', length: 255)]
    private string $zipcode;

    public function __construct(
        City $city,
        string $name,
        string $slug,
        string $zipcode,
    ) {
        $this->city = $city;
        $this->name = $name;
        $this->slug = $slug;
        $this->zipcode = $zipcode;
    }

    //------------------------------------------------------------------------

    public function __toString(): string
    {
        return $this->name.' ('.$this->zipcode.')';
    }

    public function getPrefixedName(): string
    {
        return 'à '.$this->name;
    }

    //------------------------------------------------------------------------
    // Getters & Setters
    //------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
