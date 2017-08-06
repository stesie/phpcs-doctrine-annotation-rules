<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use Doctrine\Common\Collections\Collection;
use DoctrineAnnotationCodingStandard\ImportClassMap;

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

    /**
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @return string
     */
    public function toString(string $namespace = null, ImportClassMap $imports): string
    {
        $collectionClass = parent::toString($namespace, $imports);

        return \sprintf('%s[]|%s', $this->itemType->toString($namespace, $imports), $collectionClass);
    }
}
