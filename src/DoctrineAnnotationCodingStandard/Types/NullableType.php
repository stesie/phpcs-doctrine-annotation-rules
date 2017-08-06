<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;

class NullableType implements Type, QualifyableObjectType
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
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @return string
     */
    public function toString(string $namespace = null, ImportClassMap $imports): string
    {
        return \sprintf('%s|null', $this->itemType->toString($namespace, $imports));
    }
}
