# Doctrine Annotation Coding Standard

[![Build Status](https://travis-ci.org/stesie/phpcs-doctrine-annotation-rules.svg?branch=master)](https://travis-ci.org/stesie/phpcs-doctrine-annotation-rules)
[![Coverage Status](https://coveralls.io/repos/github/stesie/phpcs-doctrine-annotation-rules/badge.svg?branch=master)](https://coveralls.io/github/stesie/phpcs-doctrine-annotation-rules?branch=master)
![PHPStan](https://img.shields.io/badge/style-level%207-brightgreen.svg?style=flat-square&label=phpstan)

Doctrine Annotation Coding Standard for [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) provides
some additional sniffs centered on DocBlock annotations for [Doctrine ORM](github.com/doctrine/doctrine2/).

*This is currently very much work in progress and not yet very useful*

Ideas for sniffs involve:

* make sure all JOIN mappings have a `@JoinColumn` annotation, that explicitly states `nullable`
  (this is because the default value `true` is unexpected to many)
* make sure `@var` annotation exists and is in sync with the ORM configuration

# Installation

The recommended way to install Doctrine Annotation Coding Standard is [through Composer](http://getcomposer.org).

```JSON
{
	"require-dev": {
		"stesie/phpcs-doctrine-annotation-rules": "^0.0"
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
