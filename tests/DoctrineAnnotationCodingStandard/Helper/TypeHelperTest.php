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
use DoctrineAnnotationCodingStandard\Types\UnqualifiedObjectType;
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
        $this->assertEquals($type, TypeHelper::fromString($typeString, new ImportClassMap()));
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
        $this->assertEquals(new ArrayType(new IntegerType()), TypeHelper::fromString('int[]', new ImportClassMap()));
    }

    public function testFromStringExplicitArrayType()
    {
        $this->assertEquals(new ArrayType(new IntegerType()), TypeHelper::fromString('array|int[]', new ImportClassMap()));
    }

    public function testFromStringWithNullablePlainType()
    {
        $this->assertEquals(new NullableType(new IntegerType()), TypeHelper::fromString('null|int', new ImportClassMap()));
    }

    public function testFromStringWithMultipleNull()
    {
        $this->assertEquals(new NullableType(new BooleanType()), TypeHelper::fromString('null|boolean|null', new ImportClassMap()));
    }

    public function testFromStringWithUntypedCollection()
    {
        $type = TypeHelper::fromString('\\Doctrine\\Common\\Collections\\Collection', new ImportClassMap());
        $this->assertEquals(new CollectionType(new MixedType()), $type);
    }

    public function testFromStringWithFqcn()
    {
        $this->assertEquals(new ObjectType(\DateTime::class), TypeHelper::fromString('\\DateTime', new ImportClassMap()));
    }

    public function testFromStringWithUnqualifiedClassname()
    {
        $this->assertEquals(new UnqualifiedObjectType('Foo'), TypeHelper::fromString('Foo', new ImportClassMap()));
    }
}
