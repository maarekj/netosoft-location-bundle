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
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Table;
use Netosoft\LocationBundle\Repository\RegionRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[Entity(repositoryClass: RegionRepository::class)]
#[Table(name: 'location__region')]
#[Index(columns: ['slug'], name: 'idx_location__region__slug')]
class Region
{
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Country::class, inversedBy: 'regions')]
    #[NotNull]
    private Country $country;

    #[Column(name: 'name', type: 'string', length: 255)]
    #[NotBlank]
    private string $name;

    #[Column(name: 'slug', type: 'string', length: 255)]
    #[NotBlank]
    private string $slug;

    #[Column(name: 'code', type: 'string', length: 100)]
    private string $code;

    #[Column(name: 'prefix', type: 'string', length: 50)]
    #[NotBlank]
    private string $prefix;

    /** @var Collection<array-key, County> */
    #[OneToMany(targetEntity: County::class, mappedBy: 'region', cascade: ['all'], orphanRemoval: true)]
    #[OrderBy(value: ['name' => 'ASC'])]
    private Collection $counties;

    //------------------------------------------------------------------------
    public function __construct(
        Country $country,
        string $name,
        string $slug,
        string $code,
        string $prefix,
    ) {
        $this->counties = new ArrayCollection();
        $this->country = $country;
        $this->name = $name;
        $this->slug = $slug;
        $this->code = $code;
        $this->prefix = $prefix;
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

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): static
    {
        $this->country = $country;

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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

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

    /** @return Collection<array-key, County> */
    public function getCounties(): Collection
    {
        return $this->counties;
    }

    /** @param Collection<array-key, County> $counties */
    public function setCounties(Collection $counties): static
    {
        $this->counties = $counties;

        return $this;
    }
}
