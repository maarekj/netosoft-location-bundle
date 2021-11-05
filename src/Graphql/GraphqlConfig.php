<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\Graphql;

use JmvDevelop\GraphqlGenerator\Schema\Argument;
use JmvDevelop\GraphqlGenerator\Schema\ObjectField;
use JmvDevelop\GraphqlGenerator\Schema\ObjectType;
use JmvDevelop\GraphqlGenerator\Schema\QueryField;
use JmvDevelop\GraphqlGenerator\Schema\SchemaDefinition;
use JmvDevelop\GraphqlGenerator\SchemaGenerator\ObjectField\CallbackObjectFieldGenerator;
use JmvDevelop\GraphqlGenerator\SchemaGenerator\ObjectField\ObjectFieldGenerator;
use Netosoft\LocationBundle\Entity\City;
use Netosoft\LocationBundle\Entity\Country;
use Netosoft\LocationBundle\Entity\County;
use Netosoft\LocationBundle\Entity\District;
use Netosoft\LocationBundle\Entity\Region;
use Netosoft\LocationBundle\ValueObject\AddressObject;
use Nette\PhpGenerator\Method;

final class GraphqlConfig
{
    /**
     * @param SchemaDefinition                                        $schema
     * @param callable(SchemaDefinition, string, string, string):void $addPagerType
     */
    public function __construct(
        private SchemaDefinition $schema,
        private $addPagerType,
    ) {
    }

    public function addTypes(): void
    {
        $assertIntNotNull = new CallbackObjectFieldGenerator(function (ObjectType $type, ObjectField $field, Method $method): void {
            $method->addBody(\sprintf('
            $v = $root->get%s();
            if (!is_int($v)) {
                throw new \RuntimeException("must be int");
            }
            return $v;
            ', \ucfirst($field->getName())))->setFinal();
        });

        $collectionGetValues = new CallbackObjectFieldGenerator(function (ObjectType $type, ObjectField $field, Method $method): void {
            $method->addBody(\sprintf('return \array_values($root->get%s()->getValues());', \ucfirst($field->getName())))->setFinal();
        });

        $callMethod = function (?string $name = null): ObjectFieldGenerator {
            return new CallbackObjectFieldGenerator(function (ObjectType $type, ObjectField $field, Method $method) use ($name): void {
                $method->addBody(\sprintf('return $root->%s();', null === $name ? $field->getName() : $name))->setFinal();
            });
        };

        $addPagerType = $this->addPagerType;
        $addPagerType($this->schema, 'Location_PagerAddress', 'Location_Address', AddressObject::class);
        $addPagerType($this->schema, 'Location_PagerDistrict', 'Location_District', District::class);
        $addPagerType($this->schema, 'Location_PagerCity', 'Location_City', City::class);
        $addPagerType($this->schema, 'Location_PagerCounty', 'Location_County', County::class);
        $addPagerType($this->schema, 'Location_PagerRegion', 'Location_Region', Region::class);
        $addPagerType($this->schema, 'Location_PagerCountry', 'Location_Country', Country::class);

        $this->schema->addType(ObjectType::create(
            name: 'Location_Address',
            rootType: '\\'.AddressObject::class,
            fields: [
                ObjectType::field(name: 'street', type: 'String'),
                ObjectType::field(name: 'lat', type: 'Float'),
                ObjectType::field(name: 'lng', type: 'Float'),
                ObjectType::field(name: 'city', type: 'Location_City!'),
                ObjectType::field(name: 'district', type: 'Location_District'),
                ObjectType::field(name: 'zipcode', type: 'String!'),
                ObjectType::field(name: 'complement', type: 'String'),
            ],
        ));

        $this->schema->addType(ObjectType::create(
            name: 'Location_District',
            rootType: '\\'.District::class,
            fields: [
                ObjectType::field(name: 'id', type: 'Int!', generator: $assertIntNotNull),
                ObjectType::field(name: 'city', type: 'Location_City!'),
                ObjectType::field(name: 'name', type: 'String!'),
                ObjectType::field(name: 'slug', type: 'String!'),
                ObjectType::field(name: 'zipcode', type: 'String!'),
            ]
        ));

        $this->schema->addType(ObjectType::create(
            name: 'Location_City',
            rootType: '\\'.City::class,
            fields: [
                ObjectType::field(name: 'id', type: 'Int!', generator: $assertIntNotNull),
                ObjectType::field(name: 'county', type: 'Location_County!'),
                ObjectType::field(name: 'name', type: 'String!'),
                ObjectType::field(name: 'slug', type: 'String!'),
                ObjectType::field(name: 'zipcode', type: 'String!'),
                ObjectType::field(name: 'districts', type: '[Location_District!]!', generator: $collectionGetValues),
                ObjectType::field(name: 'isCounty', type: 'Boolean!', generator: $callMethod()),
            ]
        ));

        $this->schema->addType(ObjectType::create(
            name: 'Location_County',
            rootType: '\\'.County::class,
            fields: [
                ObjectType::field(name: 'id', type: 'Int!', generator: $assertIntNotNull),
                ObjectType::field(name: 'region', type: 'Location_Region!'),
                ObjectType::field(name: 'name', type: 'String!'),
                ObjectType::field(name: 'slug', type: 'String!'),
                ObjectType::field(name: 'code', type: 'String!'),
                ObjectType::field(name: 'prefix', type: 'String!'),
            ]
        ));

        $this->schema->addType(ObjectType::create(
            name: 'Location_Region',
            rootType: '\\'.Region::class,
            fields: [
                ObjectType::field(name: 'id', type: 'Int!', generator: $assertIntNotNull),
                ObjectType::field(name: 'country', type: 'Location_Country!'),
                ObjectType::field(name: 'name', type: 'String!'),
                ObjectType::field(name: 'slug', type: 'String!'),
                ObjectType::field(name: 'code', type: 'String!'),
                ObjectType::field(name: 'prefix', type: 'String!'),
                ObjectType::field(name: 'counties', type: '[Location_County!]!', generator: $collectionGetValues),
            ]
        ));

        $this->schema->addType(ObjectType::create(
            name: 'Location_Country',
            rootType: '\\'.Country::class,
            fields: [
                ObjectType::field(name: 'id', type: 'Int!', generator: $assertIntNotNull),
                ObjectType::field(name: 'name', type: 'String!'),
                ObjectType::field(name: 'isoCode', type: 'String!'),
                ObjectType::field(name: 'slug', type: 'String!'),
                ObjectType::field(name: 'prefix', type: 'String!'),
                ObjectType::field(name: 'regions', type: '[Location_Region!]!', generator: $collectionGetValues),
            ]
        ));

        $this->schema->addQueryField(QueryField::create(name: 'location_search_address', type: '[Location_Address!]!', args: [
            Argument::create(name: 'query', type: 'String!'),
            Argument::create(name: 'pointLat', type: 'Float'),
            Argument::create(name: 'pointLng', type: 'Float'),
        ]));
    }

    public function addFields(): void
    {
        $this->schema->addQueryField(QueryField::create(name: 'location_search_city', type: 'Location_PagerCity!', args: [
            Argument::create(name: 'page', type: 'Int!'),
            Argument::create(name: 'maxPerPage', type: 'Int!'),
            Argument::create(name: 'query', type: 'String!'),
        ]));

        $this->addStrictField(
            type: 'Location_District',
            name: 'location_district',
            strictName: 'strict_location_district',
            args: [
                Argument::create(name: 'id', type: 'Int!'),
            ],
        );

        $this->addStrictField(
            type: 'Location_City',
            name: 'location_city',
            strictName: 'strict_location_city',
            args: [
                Argument::create(name: 'id', type: 'Int!'),
            ],
        );

        $this->addStrictField(
            type: 'Location_County',
            name: 'location_county',
            strictName: 'strict_location_county',
            args: [
                Argument::create(name: 'id', type: 'Int!'),
            ],
        );

        $this->addStrictField(
            type: 'Location_Region',
            name: 'location_region',
            strictName: 'strict_location_region',
            args: [
                Argument::create(name: 'id', type: 'Int!'),
            ],
        );

        $this->addStrictField(
            type: 'Location_Country',
            name: 'location_country',
            strictName: 'strict_location_country',
            args: [
                Argument::create(name: 'id', type: 'Int!'),
            ],
        );
    }

    /** @param list<Argument> $args */
    private function addStrictField(string $name, string $strictName, string $type, string $ns = 'QueryField', array $args = [], string $description = ''): void
    {
        $this->schema->addQueryField(QueryField::create(name: $name, type: $type, ns: $ns, args: $args, description: $description, autoResolveReturnArg: null));
        $this->schema->addQueryField(QueryField::create(name: $strictName, type: $type.'!', ns: $ns, args: $args, description: $description, autoResolveReturnArg: null));
    }
}
