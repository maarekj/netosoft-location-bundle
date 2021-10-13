<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\DataGouvClient;

final class CacheUtils
{
    /**
     * @param array<string, string|bool|float|null|int> $array
     */
    public static function createCacheKey(array $array): string
    {
        \ksort($array);
        $arrayToCached = [];
        foreach ($array as $key => $value) {
            $arrayToCached[] = [$key, $value];
        }

        $json = \json_encode($arrayToCached);
        if (false === $json) {
            throw new \RuntimeException('invalid key');
        }

        return \rawurlencode($json);
    }
}
