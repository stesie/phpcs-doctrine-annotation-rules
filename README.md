# Doctrine Annotation Coding Standard

[![Build Status](https://travis-ci.org/stesie/phpcs-doctrine-annotation-rules.svg?branch=master)](https://travis-ci.org/stesie/phpcs-doctrine-annotation-rules)
[![Coverage Status](https://coveralls.io/repos/github/stesie/phpcs-doctrine-annotation-rules/badge.svg?branch=master)](https://coveralls.io/github/stesie/phpcs-doctrine-annotation-rules?branch=master)
![PHPStan](https://img.shields.io/badge/style-level%207-brightgreen.svg?style=flat-square&label=phpstan)

Doctrine Annotation Coding Standard for [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) provides
some additional sniffs centered on DocBlock annotations for [Doctrine ORM](github.com/doctrine/doctrine2/).

# Sniffs included in this standard

:wrench: = [Automatic errors fixing](#fixing-errors-automatically)

### DoctrineAnnotationCodingStandard.Commenting.ImplicitNullableJoinColumn

Applies to DocBlocks of properties that are mapped as either `@ORM\ManyToOne` or `@ORM\OneToOne`.

* Checks for missing `@ORM\JoinColumn` annotation
* If `@ORM\JoinColumn` exists, checks if `nullable` is implicitly assumed to be `true`

The default value of `nullable` of `@ORM\JoinColumn` is `true` (as opposed to `@ORM\Column`),
which many DEVs are unaware of and hence have NULL-able associations where they should not have ones.
This sniff ensures that the nullable-choice is made explicitly.


### DoctrineAnnotationCodingStandard.Commenting.VarTag :wrench:

Applies to all DocBlocks of Doctrine-mapped properties.

* Checks for missing `@var` tag
* Checks the type stated by `@var` against actual type (according to Doctrine mapping)

This sniff supports automatic fixing with `phpcbf`.

# Installation

The recommended way to install Doctrine Annotation Coding Standard is [through Composer](http://getcomposer.org).

```JSON
{
	"require-dev": {
		"stesie/phpcs-doctrine-annotation-rules": "dev-master"
	}
}
```

Keep in mind that this is not a full coding standard, it just augments existing ones with extra checks
on Doctrine annotations.  If unsure, I highly recommend having a look at
[Slevomat Coding Standard](https://github.com/slevomat/coding-standard/).

## Using the standard as a whole

Simply mention this (additional) standard in `ruleset.xml`:

```xml
<?xml version="1.0"?>
<ruleset name="AcmeProject">
	<rule ref="vendor/stesie/phpcs-doctrine-annotation-rules/src/DoctrineAnnotationCodingStandard/ruleset.xml" />
	<!-- additional standards like slevomat -->
</ruleset>
```

To check your code base for violations, run `PHP_CodeSniffer` from the command line:

```
vendor/bin/phpcs --standard=ruleset.xml --extensions=php -sp src tests
```

## Fixing errors automatically

Sniffs in this standard marked by the :wrench: symbol support 
[automatic fixing of coding standard violations](#fixing-errors-automatically).
To fix your code automatically, run phpcbf insteand of phpcs:

```
vendor/bin/phpcbf --standard=ruleset.xml --extensions=php -sp src tests
```
