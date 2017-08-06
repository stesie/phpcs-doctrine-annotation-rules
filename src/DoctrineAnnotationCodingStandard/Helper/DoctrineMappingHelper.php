<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Helper;

use Doctrine\ORM\Mapping;

class DoctrineMappingHelper
{
    /**
     * @param array $annotations
     * @return bool
     */
    public static function isDoctrineMappedProperty(array $annotations): bool
    {
        foreach ($annotations as $doctrineTag) {
            switch (get_class($doctrineTag)) {
                case Mapping\Column::class:
                case Mapping\Embedded::class:
                case Mapping\OneToOne::class:
                case Mapping\OneToMany::class:
                case Mapping\ManyToOne::class:
                case Mapping\ManyToMany::class:
                    return true;
            }
        }

        return false;
    }

    /**
     * @param array $annotations
     * @return bool
     */
    public static function isDoctrineJoin(array $annotations): bool
    {
        foreach ($annotations as $doctrineTag) {
            switch (get_class($doctrineTag)) {
                case Mapping\OneToOne::class:
                case Mapping\OneToMany::class:
                case Mapping\ManyToOne::class:
                case Mapping\ManyToMany::class:
                    return true;
            }
        }

        return false;
    }

    /**
     * @param string $doctrineType
     * @return string
     */
    public static function getTypeFromDoctrineType(string $doctrineType): string
    {
        switch ($doctrineType) {
            case 'bigint':
            case 'integer':
            case 'smallint':
                return 'int';

            case 'float':
                return 'float';

            case 'decimal':
            case 'string':
            case 'text':
            case 'guid':
                return 'string';

            case 'binary':
            case 'blob':
                return 'resource';

            case 'boolean':
                return 'bool';

            case 'date':
            case 'datetime':
            case 'datetimez':
            case 'time':
                return '\\DateTime';

            case 'date_immutable':
            case 'datetime_immutable':
            case 'datetimez_immutable':
            case 'time_immutable':
                return '\\DateTimeImmutable';

            case 'dateinterval':
                return '\\DateInterval';

            case 'array':
            case 'simple_array':
            case 'json':
            case 'json_array':
                return 'array';

            case 'object':
                return 'object';
        }

        // Entity types just fall through
        return $doctrineType;
    }
}
