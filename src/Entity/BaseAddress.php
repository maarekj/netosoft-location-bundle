<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Netosoft\LocationBundle\ValueObject\AddressObject;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[MappedSuperclass]
class BaseAddress
{
    #[Column(type: 'text', nullable: true)]
    #[Length(max: 400)]
    private ?string $street;

    #[Column(type: 'float', nullable: true)]
    private ?float $lat;

    #[Column(type: 'float', nullable: true)]
    private ?float $lng;

    #[ManyToOne(targetEntity: City::class)]
    #[NotNull]
    private City $city;

    #[ManyToOne(targetEntity: District::class)]
    #[NotNull]
    private ?District $district;

    #[Column(type: 'text', nullable: false)]
    #[NotBlank]
    #[Length(max: 20)]
    private string $zipcode;

    #[Column(type: 'text', nullable: true)]
    #[Length(max: 400)]
    private ?string $complement;

    public function __construct(?string $street, City $city, ?District $district, string $zipcode, ?string $complement, ?float $lat, ?float $lng)
    {
        $this->street = $street;
        $this->city = $city;
        $this->district = $district;
        $this->zipcode = $zipcode;
        $this->complement = $complement;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    #[Callback]
    public function validateCityAndDistrict(ExecutionContextInterface $context): void
    {
        $district = $this->district;
        if (null !== $district) {
            if ($district->getCity()->getId() !== $this->getCity()->getId()) {
                $context
                    ->buildViolation("Le quartier n'existe pas dans la ville selectionnée.")
                    ->atPath('district')
                    ->addViolation();
            }
        }
    }

    public function mergeObject(AddressObject $address): void
    {
        $this->setStreet($address->getStreet());
        $this->setCity($address->getCity());
        $this->setDistrict($address->getDistrict());
        $this->setZipcode($address->getZipcode());
        $this->setComplement($address->getComplement());
        $this->setLat($address->getLat());
        $this->setLng($address->getLng());
    }

    public function toObject(): AddressObject
    {
        return new AddressObject(
            street: $this->getStreet(),
            city: $this->getCity(),
            district: $this->getDistrict(),
            zipcode: $this->getZipcode(),
            complement: $this->getComplement(),
            lat: $this->getLat(),
            lng: $this->getLng(),
        );
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

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

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    public function setDistrict(?District $district): self
    {
        $this->district = $district;

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

    public function getCountry(): string
    {
        return $this->getCity()->getCounty()->getRegion()->getCountry()->getIsoCode();
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }
}
