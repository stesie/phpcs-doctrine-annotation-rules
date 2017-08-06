<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Helper;

use Doctrine\ORM\Mapping;
use DoctrineAnnotationCodingStandard\Helper\DoctrineMappingHelper;
use PHPUnit\Framework\TestCase;

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
}
