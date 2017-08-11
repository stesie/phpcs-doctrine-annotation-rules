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
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @return string
     */
    public function toString(string $namespace = null, ImportClassMap $imports): string
    {
        $alias = $imports->aliasByClass($this->fqcn);

        if ($alias !== null) {
            return $alias;
        } elseif ($namespace === null) {
            return $this->fqcn;
        } elseif (strpos($this->fqcn, $namespace) === 0) {
            return substr($this->fqcn, strlen($namespace) + 1);
        } else {
            return '\\' . $this->fqcn;
        }
    }

    /**
     * @param Type $other
     * @return bool
     */
    public function isEqual(Type $other): bool
    {
        return $other instanceof self &&
            $this->fqcn === $other->fqcn;
    }
}
