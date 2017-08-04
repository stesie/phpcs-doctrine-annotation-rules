<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Sniffs\Commenting;

use DoctrineAnnotationCodingStandard\Sniffs\Commenting\ImplicitNullableJoinColumnSniff;
use DoctrineAnnotationCodingStandardTests\Sniffs\TestCase;

class ImplicitNullableJoinColumnSniffTest extends TestCase
{
    public function testNoJoinColumn()
    {
        $file = $this->checkFile(__DIR__ . '/data/JoinNoJoinColumn.inc', ImplicitNullableJoinColumnSniff::class);
        $this->assertSniffError($file, 9, ImplicitNullableJoinColumnSniff::CODE_NO_JOIN_COLUMN);
    }

    public function testCorrectNonNullJoinColumn()
    {
        $file = $this->checkFile(__DIR__ . '/data/JoinNonNullJoinColumn.inc', ImplicitNullableJoinColumnSniff::class);
        $this->assertNoSniffErrors($file);
    }

    public function testImplicitNullable()
    {
        $file = $this->checkFile(__DIR__ . '/data/JoinImplicitNullable.inc', ImplicitNullableJoinColumnSniff::class);
        $this->assertSniffError($file, 9, ImplicitNullableJoinColumnSniff::CODE_NO_NULLABLE_PROPERTY);
    }
}
