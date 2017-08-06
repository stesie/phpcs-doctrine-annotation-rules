<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use Doctrine\Common\Collections\Collection;

class CollectionType extends ObjectType implements QualifyableObjectType
{
    use QualifyViaItemTypeDelegationTrait;

    /**
     * @var Type
     */
    private $itemType;

    public function __construct(Type $itemType)
    {
        parent::__construct(Collection::class);

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
