<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;

class ObjectType implements Type
{
    /**
     * @var string
     */
    private $fqcn;

    public function __construct(string $fqcn)
    {
        if ($fqcn[0] === '\\') {
            $fqcn = substr($fqcn, 1);
        }

        $this->fqcn = $fqcn;
    }

    /**
     * @return string
     */
    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    /**
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @return string
     */
    public function toString(string $namespace = null, ImportClassMap $imports): string
    {
        $alias = $imports->aliasByClass($this->fqcn);

        if ($alias !== false) {
            return $alias;
        } elseif ($namespace === null) {
            return $this->fqcn;
        } else {
            return '\\' . $this->fqcn;
        }
    }
}
