<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Helper;

use DoctrineAnnotationCodingStandard\Helper\TypeHelper;
use DoctrineAnnotationCodingStandard\ImportClassMap;
use DoctrineAnnotationCodingStandard\Types\AnyObjectType;
use DoctrineAnnotationCodingStandard\Types\ArrayType;
use DoctrineAnnotationCodingStandard\Types\BooleanType;
use DoctrineAnnotationCodingStandard\Types\CollectionType;
use DoctrineAnnotationCodingStandard\Types\FloatType;
use DoctrineAnnotationCodingStandard\Types\IntegerType;
use DoctrineAnnotationCodingStandard\Types\MixedType;
use DoctrineAnnotationCodingStandard\Types\NullableType;
use DoctrineAnnotationCodingStandard\Types\ObjectType;
use DoctrineAnnotationCodingStandard\Types\ResourceType;
use DoctrineAnnotationCodingStandard\Types\StringType;
use DoctrineAnnotationCodingStandard\Types\Type;
use PHPUnit\Framework\TestCase;

class TypeHelperTest extends TestCase
{
    /**
     * @dataProvider plainTypesProvider
     * @param string $typeString
     * @param Type $type
     */
    public function testFromStringWithPlainTypes(string $typeString, Type $type)
    {
        $this->assertEquals($type, TypeHelper::fromString($typeString, null, new ImportClassMap()));
    }

    public function plainTypesProvider(): array
    {
        return [
            [ 'int', new IntegerType() ],
            [ 'integer', new IntegerType() ],
            [ 'float', new FloatType() ],
            [ 'bool', new BooleanType() ],
            [ 'boolean', new BooleanType() ],
            [ 'string', new StringType() ],
            [ 'mixed', new MixedType() ],
            [ 'resource' ,new ResourceType() ],
            [ 'object', new AnyObjectType() ],
            [ 'array', new ArrayType(new MixedType()) ],
        ];
    }

    public function testFromStringImplicityArrayType()
    {
        $this->assertEquals(new ArrayType(new IntegerType()), TypeHelper::fromString('int[]', null, new ImportClassMap()));
    }

    public function testFromStringExplicitArrayType()
    {
        $this->assertEquals(new ArrayType(new IntegerType()), TypeHelper::fromString('array|int[]', null, new ImportClassMap()));
    }

    public function testFromStringWithNullablePlainType()
    {
        $this->assertEquals(new NullableType(new IntegerType()), TypeHelper::fromString('null|int', null, new ImportClassMap()));
    }

    public function testFromStringWithMultipleNull()
    {
        $this->assertEquals(new NullableType(new BooleanType()), TypeHelper::fromString('null|boolean|null', null, new ImportClassMap()));
    }

    public function testFromStringWithUntypedCollection()
    {
        $type = TypeHelper::fromString('\\Doctrine\\Common\\Collections\\Collection', null, new ImportClassMap());
        $this->assertEquals(new CollectionType(new MixedType()), $type);
    }

    public function testFromStringWithUntypedCollectionViaUse()
    {
        $classMap = new ImportClassMap();
        $classMap->add('Collection', 'Doctrine\\Common\\Collections\\Collection');

        $type = TypeHelper::fromString('Collection', null, $classMap);
        $this->assertEquals(new CollectionType(new MixedType()), $type);
    }

    public function testFromStringWithTypedCollection()
    {
        $classMap = new ImportClassMap();
        $classMap->add('Collection', 'Doctrine\\Common\\Collections\\Collection');
        $classMap->add('Customer', 'AppBundle\\Entity\\Customer');

        $type = TypeHelper::fromString('Collection|Customer[]', null, $classMap);
        $this->assertEquals(new CollectionType(new ObjectType('AppBundle\\Entity\\Customer')), $type);
    }

    public function testFromStringWithTypedCollectionWithSpaces()
    {
        $classMap = new ImportClassMap();
        $classMap->add('Collection', 'Doctrine\\Common\\Collections\\Collection');
        $classMap->add('Customer', 'AppBundle\\Entity\\Customer');

        $type = TypeHelper::fromString('Collection | Customer[]', null, $classMap);
        $this->assertEquals(new CollectionType(new ObjectType('AppBundle\\Entity\\Customer')), $type);
    }

    public function testFromStringWithFqcn()
    {
        $this->assertEquals(new ObjectType(\DateTime::class), TypeHelper::fromString('\\DateTime', null, new ImportClassMap()));
    }

    public function testFromStringWithUnqualifiedClassname()
    {
        $this->assertEquals(new ObjectType('Foo'), TypeHelper::fromString('Foo', null, new ImportClassMap()));
    }
}
