<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;

class VarTagMissing
{
    /**
     * @ORM\Column(name="foo", type="integer")
     */
    private $foo;
}