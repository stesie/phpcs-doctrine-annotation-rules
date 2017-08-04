# Doctrine Annotation Coding Standard

[![Build Status](https://travis-ci.org/stesie/phpcs-doctrine-annotation-rules.svg?branch=master)](https://travis-ci.org/stesie/phpcs-doctrine-annotation-rules)
[![Coverage Status](https://coveralls.io/repos/github/stesie/phpcs-doctrine-annotation-rules/badge.svg?branch=master)](https://coveralls.io/github/stesie/phpcs-doctrine-annotation-rules?branch=master)
![PHPStan](https://img.shields.io/badge/style-level%207-brightgreen.svg?style=flat-square&label=phpstan)

Doctrine Annotation Coding Standard for [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) provides
some additional sniffs centered on DocBlock annotations for [Doctrine ORM](github.com/doctrine/doctrine2/).

*This is currently very much work in progress and not yet in releasable state*

Ideas for sniffs involve:

* make sure all JOIN mappings have a `@JoinColumn` annotation, that explicitly states `nullable`
  (this is because the default value `true` is unexpected to many)
* make sure `@var` annotation exists and is in sync with the ORM configuration
