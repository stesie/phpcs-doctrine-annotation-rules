<?php declare(strict_types = 1);

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

    /**
     * @param string|null $namespace
     * @param string[] $imports
     * @return Type
     */
    public function qualify(string $namespace = null, array $imports): Type
    {
        $parts = explode('\\', $this->className);
        $key = strtolower($parts[0]);

        if (isset($imports[$key])) {
            $parts[0] = $imports[$key];
            $fqcn = implode('\\', $parts);
        } elseif ($namespace === null) {
            $fqcn = $this->className;
        } else {
            $fqcn = $namespace . '\\' . $this->className;
        }

        return new ObjectType($fqcn);
    }
}
