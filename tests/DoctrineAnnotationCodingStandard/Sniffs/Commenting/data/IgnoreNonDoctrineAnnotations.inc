<?php

namespace Foo\Bar;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class IgnoreNonDoctrineAnnotations
{
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     * @var string
     */
    protected $companyName;
}

