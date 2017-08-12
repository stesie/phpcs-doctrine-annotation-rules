<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;

class JoinExplicitNullable
{
    /**
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumn(nullable  =  true)
     */
    private $foo;
}
