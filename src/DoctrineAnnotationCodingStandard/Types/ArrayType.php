<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

class ArrayType implements Type
{
    /**
     * @var Type
     */
    private $itemType;

    public function __construct(Type $itemType)
    {
        $this->itemType = $itemType;
    }

    /**
     * @return Type
     */
    public function getItemType(): Type
    {
        return $this->itemType;
    }
}
