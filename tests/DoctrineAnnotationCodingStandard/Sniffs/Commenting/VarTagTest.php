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
}
