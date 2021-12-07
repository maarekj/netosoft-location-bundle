<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\ValueObject;

use JmvDevelop\SameAsBundle\SameAs;
use Netosoft\LocationBundle\Entity\BaseAddress;
use Netosoft\LocationBundle\Entity\City;
use Netosoft\LocationBundle\Entity\District;

final class AddressObject
{
    #[SameAs(class: BaseAddress::class)]
    private ?string $street;

    #[SameAs(class: BaseAddress::class)]
    private ?float $lat;

    #[SameAs(class: BaseAddress::class)]
    private ?float $lng;

    #[SameAs(class: BaseAddress::class)]
    private City $city;

    #[SameAs(class: BaseAddress::class)]
    private ?District $district;

    #[SameAs(class: BaseAddress::class)]
    private string $zipcode;

    #[SameAs(class: BaseAddress::class)]
    private ?string $complement;

    public function __construct(
        ?string $street,
        City $city,
        ?District $district,
        ?string $zipcode,
        ?string $complement,
        ?float $lat,
        ?float $lng,
    ) {
        $this->street = $street;
        $this->city = $city;
        $this->district = $district;
        $this->zipcode = null === $zipcode ? $city->getMainZipcode() : $zipcode;
        $this->complement = $complement;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function clone(): AddressObject
    {
        return new AddressObject(
            street: $this->street,
            city: $this->city,
            district: $this->district,
            zipcode: $this->zipcode,
            complement: $this->complement,
            lat: $this->lat,
            lng: $this->lng,
        );
    }

    public function getStreet(): ?string
    {
        return '' === $this->street ? null : $this->street;
    }

    public function withStreet(string $street): AddressObject
    {
        $o = $this->clone();
        $o->street = $street;

        return $o;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function withCity(City $city): AddressObject
    {
        $o = $this->clone();
        $o->city = $city;

        return $o;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function withZipcode(string $zipcode): AddressObject
    {
        $o = $this->clone();
        $o->zipcode = $zipcode;

        return $o;
    }

    public function getCountry(): string
    {
        return $this->getCity()->getCounty()->getRegion()->getCountry()->getIsoCode();
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function withComplement(?string $complement): AddressObject
    {
        $o = $this->clone();
        $o->complement = $complement;

        return $o;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function withLat(?float $lat): AddressObject
    {
        $o = $this->clone();
        $o->lat = $lat;

        return $o;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function withLng(?float $lng): AddressObject
    {
        $o = $this->clone();
        $o->lng = $lng;

        return $o;
    }

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    public function withDistrict(?District $district): AddressObject
    {
        $o = $this->clone();
        $o->district = $district;

        return $o;
    }
}
