<?php declare(strict_types=1);

namespace DoctrineAnnotationCodingStandard\Helper;

use DoctrineAnnotationCodingStandard\Types\AnyObjectType;
use DoctrineAnnotationCodingStandard\Types\BooleanType;
use DoctrineAnnotationCodingStandard\Types\FloatType;
use DoctrineAnnotationCodingStandard\Types\IntegerType;
use DoctrineAnnotationCodingStandard\Types\MixedType;
use DoctrineAnnotationCodingStandard\Types\ResourceType;
use DoctrineAnnotationCodingStandard\Types\StringType;

class TypeHelper
{
    public static function fromString(string $varTagContent)
    {
        switch ($varTagContent) {
            case 'int':
            case 'integer':
                return new IntegerType();

            case 'bool':
            case 'boolean':
                return new BooleanType();

            case 'string':
                return new StringType();

            case 'float':
                return new FloatType();

            case 'object':
                return new AnyObjectType();

            case 'mixed':
                return new MixedType();

            case 'resource':
                return new ResourceType();
        }
    }
}
