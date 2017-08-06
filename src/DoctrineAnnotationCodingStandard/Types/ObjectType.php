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
}
