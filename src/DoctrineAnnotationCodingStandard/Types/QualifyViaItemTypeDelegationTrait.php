<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;

trait QualifyViaItemTypeDelegationTrait
{
    /**
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @param string $mode
     * @return Type
     */
    public function qualify(string $namespace = null, ImportClassMap $imports, string $mode = self::MODE_PHP_STANDARD): Type
    {
        if (!$this->itemType instanceof QualifyableObjectType) {
            return $this;
        }

        return new self($this->itemType->qualify($namespace, $imports, $mode));
    }
}
