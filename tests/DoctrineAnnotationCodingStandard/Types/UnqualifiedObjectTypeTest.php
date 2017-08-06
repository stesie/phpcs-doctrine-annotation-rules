<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Types;

use DoctrineAnnotationCodingStandard\ImportClassMap;
use DoctrineAnnotationCodingStandard\Types\CollectionType;
use DoctrineAnnotationCodingStandard\Types\MixedType;
use DoctrineAnnotationCodingStandard\Types\ObjectType;
use DoctrineAnnotationCodingStandard\Types\UnqualifiedObjectType;
use PHPUnit\Framework\TestCase;

class UnqualifiedObjectTypeTest extends TestCase
{
    public function testNamespaceQualificationWithoutNamespace()
    {
        $result = (new UnqualifiedObjectType('DateTime'))->qualify(null, new ImportClassMap());
        $this->assertEquals(new ObjectType(\DateTime::class), $result);
    }

    public function testNamespaceQualification()
    {
        $result = (new UnqualifiedObjectType('Bar'))->qualify('Foo', new ImportClassMap());
        $this->assertEquals(new ObjectType('Foo\\Bar'), $result);
    }

    public function testImportQualification()
    {
        $classMap = new ImportClassMap();
        $classMap->add('Bar', 'Foo\\Bar');

        $result = (new UnqualifiedObjectType('Bar'))->qualify('Something', $classMap);
        $this->assertEquals(new ObjectType('Foo\\Bar'), $result);
    }

    public function testImportQualificationSubpart()
    {
        $classMap = new ImportClassMap();
        $classMap->add('Bar', 'Foo\\Bar');

        $result = (new UnqualifiedObjectType('Bar\Baz'))->qualify('Something', $classMap);
        $this->assertEquals(new ObjectType('Foo\\Bar\Baz'), $result);
    }

    public function testQualificationToCollection()
    {
        $classMap = new ImportClassMap();
        $classMap->add('Collection', 'Doctrine\\Common\\Collections\\Collection');

        $result = (new UnqualifiedObjectType('Collection'))->qualify('Something', $classMap);
        $this->assertEquals(new CollectionType(new MixedType()), $result);
    }
}
