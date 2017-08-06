<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

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
     * @param string[] $imports
     * @return string
     */
    public function toString(string $namespace = null, array $imports): string
    {
        return $this->fqcn;
    }
}
