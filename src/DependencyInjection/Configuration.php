<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('netosoft_location');

        /** @psalm-suppress PossiblyUndefinedMethod, PossiblyNullReference, MixedMethodCall */
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('data_gouv_cache')->isRequired()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
