<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;

class VarTagEmpty
{
    /**
     * @var
     *
     * @ORM\Column(name="foo", type="integer")
     */
    private $foo;
}