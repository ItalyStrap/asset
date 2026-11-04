# ItalyStrap Asset

Handle the CSS and JS register and enqueue for WordPress.

Version 0.0.1-alpha, the API is being redesigned around value objects, see the [open issues](https://github.com/ItalyStrap/asset/issues).

## Requirements

* PHP 8.2 or later.

## Development

PHP, Composer and the database run in the Docker containers in `.docker/`, the same environment runs in CI. `make help` lists all the targets.

```bash
make up        # Start the containers
make install   # Install the dependencies and build the Codeception actors
make cs        # PHP CodeSniffer, PSR-12 and PHPCompatibility
make stan      # PHPStan at level max
make rector    # Rector dry run, PHP 8.2 and code quality sets, make rector-fix applies it
make unit      # The unit suite, WordPress is not loaded
make integration # The integration suite, WPLoader loads WordPress
make infection # Mutation tests on the unit suite, with a minimum MSI
make qa        # All the checks above
make down      # Stop and remove the containers
```

The PHP and WordPress versions are in `.docker/test.env`.
