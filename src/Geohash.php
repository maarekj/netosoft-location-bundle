<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle;

use Netosoft\LocationBundle\ValueObject\Coordinate;

/**
 * Geohash class.
 *
 * @see https://github.com/thephpleague/geotools/blob/8f364a4/src/Geohash/Geohash.php
 */
final class Geohash
{
    /**
     * The minimum length of the geo hash.
     *
     * @var int
     */
    public const MIN_LENGTH = 1;

    /**
     * The maximum length of the geo hash.
     *
     * @var int
     */
    public const MAX_LENGTH = 12;

    /**
     * @see http://en.wikipedia.org/wiki/Geohash
     * @see http://geohash.org/
     */
    public static function encode(Coordinate $coordinate, ?int $length = null): string
    {
        $length = null === $length ? self::MAX_LENGTH : $length;
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new \InvalidArgumentException('The length should be between 1 and 12.');
        }

        $bits = [16, 8, 4, 2, 1];
        $base32Chars = [
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'b', 'c', 'd', 'e', 'f', 'g',
            'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        ];

        $latitudeInterval = [-90.0, 90.0];
        $longitudeInterval = [-180.0, 180.0];
        $isEven = true;
        $bit = 0;
        $charIndex = 0;

        $geohash = '';

        while (\strlen($geohash) < $length) {
            if ($isEven) {
                $middle = ($longitudeInterval[0] + $longitudeInterval[1]) / 2;
                if ($coordinate->getLng() > $middle) {
                    $charIndex |= $bits[$bit];
                    $longitudeInterval[0] = $middle;
                } else {
                    $longitudeInterval[1] = $middle;
                }
            } else {
                $middle = ($latitudeInterval[0] + $latitudeInterval[1]) / 2;
                if ($coordinate->getLat() > $middle) {
                    $charIndex |= $bits[$bit];
                    $latitudeInterval[0] = $middle;
                } else {
                    $latitudeInterval[1] = $middle;
                }
            }

            if ($bit < 4) {
                ++$bit;
            } else {
                $geohash = $geohash.$base32Chars[$charIndex];
                $bit = 0;
                $charIndex = 0;
            }

            $isEven = !$isEven;
        }

        return $geohash;
    }

    public static function decodePoint(string $geohash): Coordinate
    {
        if (\strlen($geohash) < self::MIN_LENGTH || \strlen($geohash) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException('The length of the geo hash should be between 1 and 12.');
        }

        $bits = [16, 8, 4, 2, 1];
        $base32Chars = [
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'b', 'c', 'd', 'e', 'f', 'g',
            'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        ];

        $latitudeInterval = [-90.0, 90.0];
        $longitudeInterval = [-180.0, 180.0];

        $base32DecodeMap = [];
        $base32CharsTotal = \count($base32Chars);
        for ($i = 0; $i < $base32CharsTotal; ++$i) {
            $base32DecodeMap[$base32Chars[$i]] = $i;
        }

        $isEven = true;

        $geohashLength = \strlen($geohash);
        for ($i = 0; $i < $geohashLength; ++$i) {
            if (!isset($base32DecodeMap[$geohash[$i]])) {
                throw new \RuntimeException('This geo hash is invalid.');
            }

            $currentChar = $base32DecodeMap[$geohash[$i]];

            $bitsTotal = \count($bits);
            for ($j = 0; $j < $bitsTotal; ++$j) {
                $mask = $bits[$j];

                if ($isEven) {
                    if (($currentChar & $mask) !== 0) {
                        $longitudeInterval[0] = ($longitudeInterval[0] + $longitudeInterval[1]) / 2;
                    } else {
                        $longitudeInterval[1] = ($longitudeInterval[0] + $longitudeInterval[1]) / 2;
                    }
                } else {
                    if (($currentChar & $mask) !== 0) {
                        $latitudeInterval[0] = ($latitudeInterval[0] + $latitudeInterval[1]) / 2;
                    } else {
                        $latitudeInterval[1] = ($latitudeInterval[0] + $latitudeInterval[1]) / 2;
                    }
                }

                $isEven = !$isEven;
            }
        }

        return new Coordinate(
            lat: (($latitudeInterval[0] + $latitudeInterval[1]) / 2),
            lng: (($longitudeInterval[0] + $longitudeInterval[1]) / 2)
        );
    }
}
