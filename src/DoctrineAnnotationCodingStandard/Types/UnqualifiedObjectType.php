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
     * @param string $mode
     * @return Type
     */
    public function qualify(string $namespace = null, ImportClassMap $imports, string $mode = self::MODE_PHP_STANDARD): Type
    {
        $parts = explode('\\', $this->className);

        if ($mode === self::MODE_DOCTRINE_ANNOTATION_STYLE && count($parts) > 1) {
            return new ObjectType($this->className);
        }

        if ($parts[0] === '') {
            $fqcn = $this->className;
        } elseif ($imports->hasAlias($parts[0])) {
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

    /**
     * @param Type $other
     * @return bool
     */
    public function isEqual(Type $other): bool
    {
        return $other instanceof self &&
            $this->className === $other->className;
    }
}
