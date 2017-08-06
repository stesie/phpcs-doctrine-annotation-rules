<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandard\Types;

use Doctrine\Common\Collections\Collection;
use DoctrineAnnotationCodingStandard\ImportClassMap;

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
     * @param ImportClassMap $imports
     * @return Type
     */
    public function qualify(string $namespace = null, ImportClassMap $imports): Type
    {
        $parts = explode('\\', $this->className);

        if ($imports->hasAlias($parts[0])) {
            $parts[0] = $imports->classByAlias($parts[0]);
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

    /**
     * @param string|null $namespace
     * @param ImportClassMap $imports
     * @return string
     */
    public function toString(string $namespace = null, ImportClassMap $imports): string
    {
        return $this->className;
    }
}
