# Quanta validation

This package provides a minimalist library to validate php values.

**Require** php >= 8.0

**Installation** `composer require quanta/validation`

**Run tests** `./vendor/bin/phpunit tests`

**Testing a specific php version using docker:**

- `docker build . --build-arg PHP_VERSION=8.0 --tag quanta/validation/tests:8.0`
- `docker run --rm quanta/validation/tests:8.0`
