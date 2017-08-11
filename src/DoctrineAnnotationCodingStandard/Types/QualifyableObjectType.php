<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;

interface QualifyableObjectType
{
    const MODE_PHP_STANDARD = 'mode.php.standard';
    const MODE_DOCTRINE_ANNOTATION_STYLE = 'mode.doctrine.annotation.style';

    /**
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @param string $mode
     * @return Type
     */
    public function qualify(string $namespace = null, ImportClassMap $imports, string $mode = self::MODE_PHP_STANDARD): Type;
}
