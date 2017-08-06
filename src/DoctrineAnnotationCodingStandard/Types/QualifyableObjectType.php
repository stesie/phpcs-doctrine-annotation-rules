<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

interface QualifyableObjectType
{
    /**
     * @param string|null $namespace
     * @param string[] $imports
     * @return Type
     */
    public function qualify(string $namespace = null, array $imports): Type;
}
