<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Helper;

use Doctrine\ORM\Mapping;
use DoctrineAnnotationCodingStandard\Helper\DoctrineMappingHelper;
use DoctrineAnnotationCodingStandardTests\Sniffs\Commenting\DummySniff;
use DoctrineAnnotationCodingStandardTests\Sniffs\TestCase;

class DoctrineMappingHelperTest extends TestCase
{
    /**
     * @dataProvider annotationProvider
     * @param string $className
     * @param bool[] $mappingInfo
     */
    public function testIsDoctrineMappedProperty(string $className, array $mappingInfo)
    {
        $annotation = new $className();
        $this->assertSame($mappingInfo['isMapped'], DoctrineMappingHelper::isDoctrineMappedProperty([$annotation]));
    }

    /**
     * @dataProvider annotationProvider
     * @param string $className
     * @param bool[] $mappingInfo
     */
    public function testIsDoctrineJoin(string $className, array $mappingInfo)
    {
        $annotation = new $className();
        $this->assertSame($mappingInfo['isJoin'], DoctrineMappingHelper::isDoctrineJoin([$annotation]));
    }

    /**
     * @return array
     */
    public function annotationProvider(): array
    {
        return [
            [ 'class' => Mapping\Column::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => false ]],
            [ 'class' => Mapping\Embedded::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => false ]],
            [ 'class' => Mapping\OneToOne::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => true ]],
            [ 'class' => Mapping\OneToMany::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => true ]],
            [ 'class' => Mapping\ManyToOne::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => true ]],
            [ 'class' => Mapping\ManyToMany::class, 'mappingInfo' => [ 'isMapped' => true, 'isJoin' => true ]],

            [ 'class' => \stdClass::class, 'mappingInfo' => [ 'isMapped' => false, 'isJoin' => false ]],
        ];
    }

    /**
     * @dataProvider doctrineTypeMappingProvider
     * @param string $doctrineType
     * @param string $phpType
     */
    public function testGetTypeFromDoctrineType(string $doctrineType, string $phpType)
    {
        $this->assertSame($phpType, DoctrineMappingHelper::getTypeFromDoctrineType($doctrineType));
    }

    /**
     * @return string[][]
     */
    public function doctrineTypeMappingProvider(): array
    {
        return [
            [ 'smallint', 'int' ],
            [ 'integer', 'int' ],
            [ 'bigint', 'int' ],

            [ 'decimal', 'string' ],
            [ 'float', 'float' ],

            [ 'string', 'string' ],
            [ 'text', 'string' ],
            [ 'guid', 'string' ],

            [ 'binary', 'resource' ],
            [ 'blob', 'resource' ],

            [ 'boolean', 'bool' ],
            [ 'date', '\\DateTime' ],
            [ 'date_immutable', '\\DateTimeImmutable' ],
            [ 'datetime', '\\DateTime' ],
            [ 'datetime_immutable', '\\DateTimeImmutable' ],
            [ 'datetimez', '\\DateTime' ],
            [ 'datetimez_immutable', '\\DateTimeImmutable' ],
            [ 'time', '\\DateTime' ],
            [ 'time_immutable', '\\DateTimeImmutable' ],
            [ 'dateinterval', '\\DateInterval' ],

            [ 'array', 'array' ],
            [ 'simple_array', 'array' ],
            [ 'json', 'array' ],
            [ 'json_array', 'array' ],

            [ 'object', 'object' ],
        ];
    }

    public function testGetTypeFromAnnotationMappedColumn()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\Column(type="date") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertSame('\\DateTime', DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedEmbedded()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\Embedded(class="Foo") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertSame('Foo', DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedOneToOne()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\OneToOne(targetEntity="Foo") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertSame('Foo', DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedManyToOne()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\ManyToOne(targetEntity="Foo") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertSame('Foo', DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedOneToMany()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\OneToMany(targetEntity="Foo") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertSame('Collection|Foo[]', DoctrineMappingHelper::getMappedType($annotations));
    }

    public function testGetTypeFromAnnotationMappedManyToMany()
    {
        $this->checkString('use Doctrine\ORM\Mapping as ORM; /** @ORM\ManyToMany(targetEntity="Foo") */', DummySniff::class);
        $annotations = $this->getSniff()->getAnnotations();

        $this->assertSame('Collection|Foo[]', DoctrineMappingHelper::getMappedType($annotations));
    }
}
