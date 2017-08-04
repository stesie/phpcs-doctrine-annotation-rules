<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Sniffs\Commenting;

use DoctrineAnnotationCodingStandard\Sniffs\Commenting\VarTagMissingSniff;
use DoctrineAnnotationCodingStandardTests\Sniffs\TestCase;

class VarTagMissingSniffTest extends TestCase
{
    public function testMissingVarTagOnORMColumn()
    {
        $file = $this->checkFile(__DIR__ . '/data/VarTagMissing.inc', VarTagMissingSniff::class);
        $this->assertSniffError($file, 9, VarTagMissingSniff::CODE_NO_VAR_TAG);
    }
}
