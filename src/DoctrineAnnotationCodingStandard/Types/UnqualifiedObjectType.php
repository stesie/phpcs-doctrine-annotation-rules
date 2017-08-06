<?php declare(strict_types=1);

namespace DoctrineAnnotationCodingStandard\Types;

class UnqualifiedObjectType implements Type
{
    /**
     * @var string
     */
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }
}
