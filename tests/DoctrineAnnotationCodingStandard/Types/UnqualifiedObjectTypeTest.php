<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Types;

use DoctrineAnnotationCodingStandard\Types\CollectionType;
use DoctrineAnnotationCodingStandard\Types\MixedType;
use DoctrineAnnotationCodingStandard\Types\ObjectType;
use DoctrineAnnotationCodingStandard\Types\UnqualifiedObjectType;
use PHPUnit\Framework\TestCase;

class UnqualifiedObjectTypeTest extends TestCase
{
    public function testNamespaceQualificationWithoutNamespace()
    {
        $result = (new UnqualifiedObjectType('DateTime'))->qualify(null, []);
        $this->assertEquals(new ObjectType(\DateTime::class), $result);
    }

    public function testNamespaceQualification()
    {
        $result = (new UnqualifiedObjectType('Bar'))->qualify('Foo', []);
        $this->assertEquals(new ObjectType('Foo\\Bar'), $result);
    }

    public function testImportQualification()
    {
        $result = (new UnqualifiedObjectType('Bar'))->qualify('Something', ['bar' => 'Foo\\Bar']);
        $this->assertEquals(new ObjectType('Foo\\Bar'), $result);
    }

    public function testImportQualificationSubpart()
    {
        $result = (new UnqualifiedObjectType('Bar\Baz'))->qualify('Something', ['bar' => 'Foo\\Bar']);
        $this->assertEquals(new ObjectType('Foo\\Bar\Baz'), $result);
    }

    public function testQualificationToCollection()
    {
        $result = (new UnqualifiedObjectType('Collection'))
            ->qualify('Something', ['collection' => 'Doctrine\\Common\\Collections\\Collection']);
        $this->assertEquals(new CollectionType(new MixedType()), $result);
    }
}
