<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

trait QualifyViaItemTypeDelegationTrait
{
    /**
     * @param string|null $namespace
     * @param string[] $imports
     * @return Type
     */
    public function qualify(string $namespace = null, array $imports): Type
    {
        if (!$this->itemType instanceof QualifyableObjectType) {
            return $this;
        }

        return new self($this->itemType->qualify($namespace, $imports));
    }
}
