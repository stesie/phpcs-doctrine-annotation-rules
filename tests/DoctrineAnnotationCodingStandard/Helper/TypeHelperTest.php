<?php declare(strict_types=1);

namespace DoctrineAnnotationCodingStandardTests\Helper;

use DoctrineAnnotationCodingStandard\Helper\TypeHelper;
use DoctrineAnnotationCodingStandard\Types\AnyObjectType;
use DoctrineAnnotationCodingStandard\Types\BooleanType;
use DoctrineAnnotationCodingStandard\Types\FloatType;
use DoctrineAnnotationCodingStandard\Types\IntegerType;
use DoctrineAnnotationCodingStandard\Types\MixedType;
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
        $this->assertEquals($type, TypeHelper::fromString($typeString));
    }

    public function plainTypesProvider()
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
        ];
    }
}
