<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Helper;

use DoctrineAnnotationCodingStandard\Exception\ParseErrorException;
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

class TypeHelper
{
    public static function fromString(string $varTagContent, ImportClassMap $classMap): Type
    {
        $parts = explode('|', $varTagContent);

        if (count($parts) === 1) {
            if (substr($varTagContent, -2) === '[]') {
                return new ArrayType(self::convertPrimitiveType(substr($varTagContent, 0, -2), $classMap));
            }

            return self::convertPrimitiveType($varTagContent, $classMap);
        }

        if (in_array('null', $parts)) {
            $parts = array_filter($parts, function (string $value) {
                return $value !== 'null';
            });
            return new NullableType(self::fromString(implode('|', $parts), $classMap));
        }

        if (in_array('array', $parts)) {
            $parts = array_filter($parts, function (string $value) {
                return $value !== 'array';
            });
            $itemType = self::fromString(implode('|', $parts), $classMap);

            if (!$itemType instanceof ArrayType) {
                throw new ParseErrorException('itemtype of array not an array');
            }

            return $itemType;
        }

        throw new ParseErrorException('type string not understood: ' . $varTagContent);
    }

    /**
     * @param string $varTagContent
     * @param ImportClassMap $classMap
     * @return Type
     */
    private static function convertPrimitiveType(string $varTagContent, ImportClassMap $classMap): Type
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

            case 'array':
                return new ArrayType(new MixedType());

            case '\\Doctrine\\Common\\Collections\\Collection':
                return new CollectionType(new MixedType());
        }

        if ($varTagContent[0] === '\\') {
            return new ObjectType(substr($varTagContent, 1));
        }

        $unqualifiedObject = new UnqualifiedObjectType($varTagContent);
        $tempQualifiedObject = $unqualifiedObject->qualify(null, $classMap);

        if ($tempQualifiedObject instanceof CollectionType) {
            return $tempQualifiedObject;
        }

        return $unqualifiedObject;
    }
}
