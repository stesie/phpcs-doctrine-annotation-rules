<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Helper;

use DateTimeImmutable;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Mapping\JoinColumn;
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

class DoctrineMappingHelper
{
    /**
     * @param array $annotations
     * @return bool
     */
    public static function isDoctrineMappedProperty(array $annotations): bool
    {
        return self::getPropertyMappingAnnotation($annotations) !== null;
    }

    /**
     * @param array $annotations
     * @return bool
     */
    public static function isDoctrineToOneJoin(array $annotations): bool
    {
        foreach ($annotations as $doctrineTag) {
            switch (get_class($doctrineTag)) {
                case Mapping\OneToOne::class:
                case Mapping\ManyToOne::class:
                    return true;
            }
        }

        return false;
    }

    /**
     * @param string $doctrineType
     * @return Type
     */
    public static function getTypeFromDoctrineType(string $doctrineType): Type
    {
        switch ($doctrineType) {
            case 'bigint':
            case 'integer':
            case 'smallint':
                return new IntegerType();

            case 'float':
                return new FloatType();

            case 'decimal':
            case 'string':
            case 'text':
            case 'guid':
                return new StringType();

            case 'binary':
            case 'blob':
                return new ResourceType();

            case 'boolean':
                return new BooleanType();

            case 'date':
            case 'datetime':
            case 'datetimez':
            case 'time':
                return new ObjectType(\DateTime::class);

            case 'date_immutable':
            case 'datetime_immutable':
            case 'datetimez_immutable':
            case 'time_immutable':
                return new ObjectType(DateTimeImmutable::class);

            case 'dateinterval':
                return new ObjectType(\DateInterval::class);

            case 'array':
            case 'simple_array':
            case 'json':
            case 'json_array':
                return new ArrayType(new MixedType());

            case 'object':
                return new AnyObjectType();
        }

        // Entity types just fall through
        return new UnqualifiedObjectType($doctrineType);
    }

    /**
     * @param array $annotations
     * @return Type
     */
    public static function getMappedType(array $annotations): Type
    {
        $mappingAnnotation = self::getPropertyMappingAnnotation($annotations);

        if ($mappingAnnotation === null) {
            throw new \InvalidArgumentException('property is not mapped');
        }

        switch (get_class($mappingAnnotation)) {
            case Mapping\Column::class:
                if ($mappingAnnotation->nullable) {
                    return new NullableType(self::getTypeFromDoctrineType($mappingAnnotation->type));
                } else {
                    return self::getTypeFromDoctrineType($mappingAnnotation->type);
                }

            case Mapping\Embedded::class:
                return new UnqualifiedObjectType($mappingAnnotation->class);

            case Mapping\OneToOne::class:
            case Mapping\ManyToOne::class:
                $objectType = new UnqualifiedObjectType($mappingAnnotation->targetEntity);

                /** @var JoinColumn|null $joinColumn */
                $joinColumn = DocBlockHelper::findAnnotationByClass(JoinColumn::class, $annotations);

                if ($joinColumn === null || $joinColumn->nullable) {
                    return new NullableType($objectType);
                }

                return $objectType;

            case Mapping\OneToMany::class:
            case Mapping\ManyToMany::class:
                return new CollectionType(new UnqualifiedObjectType($mappingAnnotation->targetEntity));

            default:
                throw new \LogicException();
        }
    }

    /**
     * @param array $annotations
     * @return Mapping\Column|Mapping\Embedded|Mapping\OneToOne|Mapping\OneToMany|Mapping\ManyToOne|Mapping\ManyToMany|null
     */
    private static function getPropertyMappingAnnotation(array $annotations)
    {
        foreach ($annotations as $doctrineTag) {
            switch (get_class($doctrineTag)) {
                case Mapping\Column::class:
                case Mapping\Embedded::class:
                case Mapping\OneToOne::class:
                case Mapping\OneToMany::class:
                case Mapping\ManyToOne::class:
                case Mapping\ManyToMany::class:
                    return $doctrineTag;
            }
        }

        return null;
    }
}
