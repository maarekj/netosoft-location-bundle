<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Table;
use Netosoft\LocationBundle\Repository\CountryRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: CountryRepository::class)]
#[Table(name: 'location__country')]
#[Index(columns: ['slug'], name: 'idx_location__country__slug')]
class Country
{
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[Column(name: 'name', type: 'string', length: 255)]
    #[NotBlank]
    private string $name;

    #[Column(name: 'iso_code', type: 'string', length: 100)]
    #[NotBlank]
    private string $isoCode;

    #[Column(name: 'slug', type: 'string', length: 100)]
    #[NotBlank]
    private string $slug;

    #[Column(name: 'prefix', type: 'string', length: 50)]
    #[NotBlank]
    private string $prefix;

    /** @var Collection<array-key, Region> */
    #[OneToMany(targetEntity: Region::class, mappedBy: 'country', cascade: ['all'], orphanRemoval: true)]
    #[OrderBy(value: ['name' => 'ASC'])]
    private Collection $regions;

    //------------------------------------------------------------------------
    public function __construct(
        string $name,
        string $isoCode,
        string $slug,
        string $prefix,
    ) {
        $this->regions = new ArrayCollection();
        $this->name = $name;
        $this->isoCode = $isoCode;
        $this->slug = $slug;
        $this->prefix = $prefix;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getPrefixedName(): string
    {
        return $this->prefix.' '.$this->name;
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

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    /** @return Collection<array-key, Region> */
    public function getRegions(): Collection
    {
        return $this->regions;
    }

    /** @param Collection<array-key, Region> $regions */
    public function setRegions(Collection $regions): static
    {
        $this->regions = $regions;

        return $this;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function setIsoCode(string $isoCode): self
    {
        $this->isoCode = $isoCode;

        return $this;
    }
}
