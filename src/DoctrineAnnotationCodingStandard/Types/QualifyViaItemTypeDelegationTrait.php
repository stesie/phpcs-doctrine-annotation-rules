<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;

trait QualifyViaItemTypeDelegationTrait
{
    /**
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @return Type
     */
    public function qualify(string $namespace = null, ImportClassMap $imports): Type
    {
        if (!$this->itemType instanceof QualifyableObjectType) {
            return $this;
        }

        return new self($this->itemType->qualify($namespace, $imports));
    }
}
