<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;

class JoinImplicitNullable
{
    /**
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumn()
     */
    private $foo;
}
