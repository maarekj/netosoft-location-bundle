<?php

namespace Netosoft\LocationBundle\Tests\DataGouvClient;

use Netosoft\LocationBundle\DataGouvClient\CacheUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\CacheItem;

/**
 * @covers \Netosoft\LocationBundle\DataGouvClient\CacheUtils
 */
final class CacheUtilsTest extends TestCase
{
    /**
     * @dataProvider provideCreateCacheKey
     * @not
     */
    public function testCreateCacheKey(array $params, string $expected)
    {
        $key = CacheUtils::createCacheKey($params);
        $this->assertEquals($expected, $key);
        CacheItem::validateKey($key);
    }

    public function provideCreateCacheKey()
    {
        yield [["lastname" => "doe"], \rawurlencode('[["lastname","doe"]]')];
        yield [["lastname" => "doe", "firstname" => "john"], \rawurlencode('[["firstname","john"],["lastname","doe"]]')];
        yield [["firstname" => "john", "lastname" => "doe"], \rawurlencode('[["firstname","john"],["lastname","doe"]]')];
        yield [["@content@" => "/{content( : )forbidden}\\"], \rawurlencode('[["@content@","\\/{content( : )forbidden}\\\\"]]')];
    }
}
