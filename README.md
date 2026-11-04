# ItalyStrap Asset

Handle the CSS and JS register and enqueue for WordPress.

Version 0.0.1-alpha, the API is being redesigned around value objects, see the [open issues](https://github.com/ItalyStrap/asset/issues).

## Requirements

* PHP 8.2 or later.

## Usage

Each script and style is an immutable value object in `ItalyStrap\Asset\Domain`, checked when it is built. The WordPress layer that registers them comes with [#5](https://github.com/ItalyStrap/asset/issues/5).

```php
use ItalyStrap\Asset\Domain\Dependencies;
use ItalyStrap\Asset\Domain\Handle;
use ItalyStrap\Asset\Domain\InlineScript;
use ItalyStrap\Asset\Domain\NoVersion;
use ItalyStrap\Asset\Domain\Position;
use ItalyStrap\Asset\Domain\Script;
use ItalyStrap\Asset\Domain\ScriptLoading;
use ItalyStrap\Asset\Domain\Strategy;
use ItalyStrap\Asset\Domain\Style;
use ItalyStrap\Asset\Domain\Translations;
use ItalyStrap\Asset\Domain\When;
use ItalyStrap\Asset\Infrastructure\BuildMetadataFile;

// The dependencies and the version written by @wordpress/scripts in build/index.asset.php
$metadata = (new BuildMetadataFile(__DIR__ . '/build/index.js'))->read();

$script = new Script(
    handle: new Handle('my-plugin'),
    source: plugins_url('build/index.js', __FILE__),
    dependencies: $metadata->dependencies()->with(new Dependencies('my-consent')),
    version: $metadata->version(),
    loading: new ScriptLoading(inFooter: true, strategy: Strategy::Defer),
    inlineScripts: [new InlineScript('window.myPlugin = {};', Position::Before)],
    translations: new Translations('my-plugin', __DIR__ . '/languages'),
    condition: new When(is_singular(...)),
);

$tag = new Script(
    handle: new Handle('my-analytics'),
    source: 'https://www.googletagmanager.com/gtag/js?id=G-XXXX',
    version: new NoVersion(),
    loading: new ScriptLoading(strategy: Strategy::Async),
    data: ['consent_category' => 'statistics'],
);

// Only inline styles, no file
$colors = new Style(new Handle('my-colors'), null, inlineStyles: ['.my-banner{color:#000}']);
```

| Version | The `ver` in the URL |
| --- | --- |
| `WordPressVersion`, the default | The version of WordPress |
| `NoVersion` | None, for external URLs |
| `FixedVersion('1.2.3')` | The given string |

The condition runs when the asset is enqueued, `Always` is the default, with `When` the asset is only registered when the callback returns false.

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
