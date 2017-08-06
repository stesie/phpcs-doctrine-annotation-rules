<?php declare(strict_types=1);

namespace DoctrineAnnotationCodingStandard\Types;

class ObjectType implements Type
{
    /**
     * @var string
     */
    private $fqcn;

    public function __construct(string $fqcn)
    {
        $this->fqcn = $fqcn;
    }
}
