<?php

namespace DoctrineAnnotationCodingStandard\Helper;

use Doctrine\ORM\Mapping;

class DoctrineMappingHelper
{
    /**
     * @param array $annotations
     * @return bool
     */
    public static function isDoctrineMappedProperty($annotations)
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
    public static function isDoctrineJoin($annotations)
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
     * @param string $className
     * @param array $annotations
     * @return object|null
     */
    public static function findAnnotationByClass($className, $annotations)
    {
        foreach ($annotations as $doctrineTag) {
            if ($className === get_class($doctrineTag)) {
                return $doctrineTag;
            }
        }

        return null;
    }
}