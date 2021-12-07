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
use Netosoft\LocationBundle\Repository\CityRepository;

#[Entity(repositoryClass: CityRepository::class)]
#[Table(name: 'location__city')]
#[Index(columns: ['slug'], name: 'idx_location__city__slug')]
#[Index(columns: ['zipcode'], name: 'idx_location__city__zipcode')]
#[Index(columns: ['name'], name: 'idx_location__city__name')]
#[Index(columns: ['zipcode', 'name'], name: 'idx_location__city__zipcode_name')]
class City implements \Stringable
{
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[ManyToOne(targetEntity: County::class, inversedBy: 'cities')]
    private County $county;

    #[Column(name: 'name', type: 'string', length: 255)]
    private string $name;

    #[Column(name: 'slug', type: 'string', length: 255)]
    private string $slug;

    #[Column(name: 'zipcode', type: 'string', length: 255)]
    private string $zipcode;

    /** @var Collection<array-key, District> */
    #[OneToMany(targetEntity: District::class, mappedBy: 'city', cascade: ['all'], orphanRemoval: true)]
    private Collection $districts;

    #[Column(name: 'is_county', type: 'boolean')]
    private bool $isCounty;

    public function __construct(
        County $county,
        string $name,
        string $slug,
        string $zipcode,
        bool $isCounty,
    ) {
        $this->county = $county;
        $this->name = $name;
        $this->slug = $slug;
        $this->zipcode = $zipcode;
        $this->isCounty = $isCounty;
        $this->districts = new ArrayCollection();
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

    /**
     * @return string[]
     * @psalm-return non-empty-list<string>
     */
    public function getZipcodeAsArray(): array
    {
        return \explode('-', $this->zipcode);
    }

    public function hasZipcode(string $zipcode): bool
    {
        $zipcodes = $this->getZipcodeAsArray();

        return \in_array($zipcode, $zipcodes);
    }

    public function getMainZipcode(): string
    {
        return \current($this->getZipcodeAsArray());
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

    public function getCounty(): County
    {
        return $this->county;
    }

    public function setCounty(County $county): self
    {
        $this->county = $county;

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

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): static
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function isCounty(): bool
    {
        return $this->isCounty;
    }

    public function setIsCounty(bool $isCounty): static
    {
        $this->isCounty = $isCounty;

        return $this;
    }

    public function hasDistricts(): bool
    {
        return false === $this->districts->isEmpty();
    }

    /** @return Collection<array-key, District> */
    public function getDistricts(): Collection
    {
        return $this->districts;
    }

    /** @param Collection<array-key, District> $districts */
    public function setDistricts(Collection $districts): self
    {
        $this->districts = $districts;

        return $this;
    }
}
