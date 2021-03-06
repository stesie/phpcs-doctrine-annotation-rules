<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Sniffs\Commenting;

use DoctrineAnnotationCodingStandard\Sniffs\Commenting\VarTagSniff;
use DoctrineAnnotationCodingStandardTests\Sniffs\TestCase;

class VarTagTest extends TestCase
{
    public function testMissingVarTagOnORMColumn()
    {
        $file = $this->checkFile(__DIR__ . '/data/VarTagMissing.inc', VarTagSniff::class);
        $this->assertSniffError($file, 9, VarTagSniff::CODE_NO_VAR_TAG);
    }

    public function testMissingVarTagFix()
    {
        $file = $this->checkFile(__DIR__ . '/data/VarTagMissing.inc', VarTagSniff::class);
        $this->assertFixedEqualsFile(__DIR__ . '/data/VarTagMissing.fixed.inc', $file);
    }

    public function testEmptyVarTagFix()
    {
        $file = $this->checkFile(__DIR__ . '/data/VarTagEmpty.inc', VarTagSniff::class);
        $this->assertFixedEqualsFile(__DIR__ . '/data/VarTagEmpty.fixed.inc', $file);
    }

    public function testWrongVarTagOnORMColumn()
    {
        $file = $this->checkFile(__DIR__ . '/data/VarTagWrong.inc', VarTagSniff::class);
        $this->assertSniffError(
            $file,
            9,
            VarTagSniff::CODE_WRONG_VAR_TAG,
            'Expected @var type of "int", got "string"'
        );
    }

    public function testWrongVarTagFix()
    {
        $file = $this->checkFile(__DIR__ . '/data/VarTagWrong.inc', VarTagSniff::class);
        $this->assertFixedEqualsFile(__DIR__ . '/data/VarTagWrong.fixed.inc', $file);
    }

    public function testCorrectVarTagOnORMColumn()
    {
        $file = $this->checkFile(__DIR__ . '/data/VarTagCorrect.inc', VarTagSniff::class);
        $this->assertNoSniffErrors($file);
    }

    public function testCorrectVarTagWithEnumType()
    {
        $file = $this->checkFile(__DIR__ . '/data/VarTagEnumTypeColumn.inc', VarTagSniff::class, ['doctrineExtraTypes' => ['CustomerType']]);
        $this->assertNoSniffErrors($file);
    }

    public function testCorrectVarTagWithFQCN()
    {
        $file = $this->checkFile(__DIR__ . '/data/VarTagWithFQCN.inc', VarTagSniff::class);
        $this->assertNoSniffErrors($file);
    }

    public function testJoinNoJoinColumnOnOneToOneInverseSide()
    {
        $file = $this->checkFile(__DIR__ . '/data/JoinNoJoinColumnOnOneToOneInverseSide.inc', VarTagSniff::class);
        $this->assertNoSniffErrors($file);
    }

    public function testAnnotationsWithClassConstants()
    {
        $file = $this->checkFile(__DIR__ . '/data/AnnotationWithClassConstants.inc', VarTagSniff::class);
        $this->assertNoSniffErrors($file);
    }
}
