<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\DataGouvClient\Model;

final class Properties
{
    public const TYPE_HOUSENUMBER = 'housenumber';
    public const TYPE_STREET = 'street';
    public const TYPE_LOCALITY = 'locality';
    public const TYPE_MUNICIPALITY = 'municipality';

    /**
     * @param string             $id          identifiant de l’adresse (clef d’interopérabilité)
     * @param Properties::TYPE_* $type        type de résultat trouvé
     * @param float|null         $score       valeur de 0 à 1 indiquant la pertinence du résultat
     * @param string|null        $housenumber numéro avec indice de répétition éventuel (bis, ter, A, B)
     * @param string|null        $name        numéro éventuel et nom de voie ou lieu dit
     * @param string|null        $postcode    code postal
     * @param string|null        $citycode    code INSEE de la commune
     * @param string|null        $city        nom de la commune
     * @param string|null        $district    nom de l’arrondissement (Paris/Lyon/Marseille)
     * @param string|null        $oldcity     code INSEE de la commune ancienne (le cas échéant)
     * @param string|null        $oldcitycode nom de la commune ancienne (le cas échéant)
     * @param string|null        $context     n° de département, nom de département et de région
     * @param string|null        $label       libellé complet de l’adresse
     * @param float|null         $x           coordonnées géographique en projection légale
     * @param float|null         $y           coordonnées géographique en projection légale
     * @param float|null         $importance  indicateur d’importance (champ technique)
     *
     * @see https://adresse.data.gouv.fr/api-doc/adresse
     */
    public function __construct(
        private string $id,
        private string $type,
        private ?float $score,
        private ?string $housenumber,
        private ?string $name,
        private ?string $postcode,
        private ?string $citycode,
        private ?string $city,
        private ?string $district,
        private ?string $oldcity,
        private ?string $oldcitycode,
        private ?string $context,
        private ?string $label,
        private ?float $x,
        private ?float $y,
        private ?float $importance,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Properties::TYPE_*
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function getHousenumber(): ?string
    {
        return $this->housenumber;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function getCitycode(): ?string
    {
        return $this->citycode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function getOldcity(): ?string
    {
        return $this->oldcity;
    }

    public function getOldcitycode(): ?string
    {
        return $this->oldcitycode;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function getImportance(): ?float
    {
        return $this->importance;
    }
}
