<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;

interface QualifyableObjectType
{
    /**
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @return Type
     */
    public function qualify(string $namespace = null, ImportClassMap $imports): Type;
}
