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

    /**
     * @param string|null $namespace
     * @param string[] $imports
     * @return string
     */
    public function toString(string $namespace = null, array $imports): string
    {
        $collectionClass = array_search(Collection::class, $imports);

        if ($collectionClass === false) {
            $collectionClass = '\\' . Collection::class;
        }

        return \sprintf('%s[]|%s', $this->itemType->toString($namespace, $imports), $collectionClass);
    }
}
