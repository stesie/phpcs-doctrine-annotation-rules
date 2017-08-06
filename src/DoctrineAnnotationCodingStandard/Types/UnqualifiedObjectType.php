<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use Doctrine\Common\Collections\Collection;

class UnqualifiedObjectType implements Type, QualifyableObjectType
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

        if ($fqcn === Collection::class) {
            return new CollectionType(new MixedType());
        }

        return new ObjectType($fqcn);
    }
}
