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
use Doctrine\ORM\Mapping\Table;
use Netosoft\LocationBundle\Repository\CountyRepository;

#[Entity(repositoryClass: CountyRepository::class)]
#[Table(name: 'location__county')]
#[Index(columns: ['slug'], name: 'idx_location__county__slug')]
#[Index(columns: ['code'], name: 'idx_location__county__code')]
class County implements \Stringable
{
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Region::class, inversedBy: 'counties')]
    private Region $region;

    #[Column(name: 'name', type: 'string', length: 255)]
    private string $name;

    #[Column(name: 'slug', type: 'string', length: 255)]
    private string $slug;

    #[Column(name: 'code', type: 'string', length: 100)]
    private string $code;

    #[Column(name: 'prefix', type: 'string', length: 50)]
    private string $prefix;

    /** @var Collection<array-key, City> */
    #[OneToMany(targetEntity: City::class, mappedBy: 'county', cascade: ['all'], orphanRemoval: true)]
    private Collection $cities;

    //------------------------------------------------------------------------
    public function __construct(
        Region $region,
        string $name,
        string $slug,
        string $code,
        string $prefix,
    ) {
        $this->cities = new ArrayCollection();
        $this->region = $region;
        $this->name = $name;
        $this->slug = $slug;
        $this->code = $code;
        $this->prefix = $prefix;
    }

    public function __toString(): string
    {
        return $this->code.' - '.$this->name;
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

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function setRegion(Region $region): static
    {
        $this->region = $region;

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

    /**
     * @return Collection<array-key, City>
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    /**
     * @param Collection<array-key, City> $cities
     */
    public function setCities(Collection $cities): static
    {
        $this->cities = $cities;

        return $this;
    }
}
