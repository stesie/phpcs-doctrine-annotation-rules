<?php

namespace DoctrineAnnotations\Helper;

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

}