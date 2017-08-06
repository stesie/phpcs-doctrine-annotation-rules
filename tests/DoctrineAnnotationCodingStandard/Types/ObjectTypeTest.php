<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;
use DoctrineAnnotationCodingStandard\Types\ObjectType;
use PHPUnit\Framework\TestCase;

class ObjectTypeTest extends TestCase
{
    public function testToStringPlain()
    {
        $type = new ObjectType(\DateTime::class);
        $this->assertSame(
            'DateTime',
            $type->toString(null, new ImportClassMap())
        );
    }

    public function testToStringWithNamespace()
    {
        $type = new ObjectType(\DateTime::class);
        $this->assertSame(
            '\\DateTime',
            $type->toString('Foo\\Bar', new ImportClassMap())
        );
    }

    public function testToStringWithAlias()
    {
        $type = new ObjectType(\DateTime::class);

        $classMap = new ImportClassMap();
        $classMap->add('Blarg', \DateTime::class);

        $this->assertSame(
            'Blarg',
            $type->toString('Foo\\Bar', $classMap)
        );
    }
}
