<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests;

use DoctrineAnnotationCodingStandard\ImportClassMap;
use PHPUnit\Framework\TestCase;

class ImportClassMapTest extends TestCase
{
    public function testForwardLookup()
    {
        $classMap = new ImportClassMap();
        $classMap->add('Foo', 'Foo\\Bar');

        $this->assertSame('Foo\\Bar', $classMap->classByAlias('Foo'));
    }

    public function testBackwardLookup()
    {
        $classMap = new ImportClassMap();
        $classMap->add('Foo', 'Foo\\Bar');

        $this->assertSame('Foo', $classMap->aliasByClass('Foo\\Bar'));
    }
}
