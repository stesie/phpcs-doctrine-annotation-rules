<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

class ArrayType implements Type, QualifyableObjectType
{
    use QualifyViaItemTypeDelegationTrait;

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

    /**
     * @param string|null $namespace
     * @param string[] $imports
     * @return string
     */
    public function toString(string $namespace = null, array $imports): string
    {
        return \sprintf('%s[]', $this->itemType->toString($namespace, $imports));
    }
}
