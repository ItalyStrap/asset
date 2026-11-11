# ItalyStrap Asset

Handle the CSS and JS register and enqueue.

The [releases](https://github.com/ItalyStrap/asset/releases) follow [semantic versioning](https://semver.org).

## Requirements

* PHP 8.2 or later.
* The WordPress functions each class calls, see the table in [The WordPress functions](#the-wordpress-functions).
* [italystrap/event](https://github.com/ItalyStrap/event), only for the `AssetsSubscriber`.

## Installation

```bash
composer require italystrap/asset
```

## Usage

Each script and style is an immutable value object, checked when it is built. The loaders register them with all their parts and enqueue them when their condition is satisfied.

```php
use ItalyStrap\Asset\BuildMetadataFile;
use ItalyStrap\Asset\Core\PluginFileUrl;
use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\InlineScript;
use ItalyStrap\Asset\NoVersion;
use ItalyStrap\Asset\Position;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\ScriptLoading;
use ItalyStrap\Asset\Strategy;
use ItalyStrap\Asset\Style;
use ItalyStrap\Asset\Translations;
use ItalyStrap\Asset\When;

// A path or a file of the Finder
$built = new SplFileInfo(__DIR__ . '/build/index.js');

// The dependencies and the version written by the build in build/index.asset.php
$metadata = (new BuildMetadataFile($built))->read();

$script = new Script(
    handle: new Handle('my-plugin'),
    source: (new PluginFileUrl(new SplFileInfo(__FILE__)))->url($built),
    dependencies: $metadata->dependencies()->with(new Dependencies('my-consent')),
    version: $metadata->version(),
    loading: new ScriptLoading(inFooter: true, strategy: Strategy::Defer),
    inlineScripts: [new InlineScript('window.myPlugin = {};', Position::Before)],
    translations: new Translations('my-plugin', new SplFileInfo(__DIR__ . '/languages')),
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
| `CoreVersion`, the default | The version of the core |
| `NoVersion` | None, for external URLs |
| `FixedVersion('1.2.3')` | The given string |

The condition runs when the asset is enqueued, `Always` is the default, with `When` the asset is only registered when the callback returns false, with `RegisterOnly` it is only registered, for a dependency of other assets.

### Load the assets

```php
use ItalyStrap\Asset\AssetLoader;
use ItalyStrap\Asset\AssetsSubscriber;
use ItalyStrap\Asset\EventName;
use ItalyStrap\Event\GlobalOrderedListenerProvider;
use ItalyStrap\Event\SubscriberRegister;

add_action(EventName::FRONT_END, static fn () => (new AssetLoader())->load($script, $tag, $colors));

// Or with italystrap/event, the priority is optional
$subscribers = new SubscriberRegister(new GlobalOrderedListenerProvider());
$subscribers->addSubscriber(new AssetsSubscriber(new AssetLoader(), [$script, $tag], EventName::ADMIN, 20));
```

| Event name | Where |
| --- | --- |
| `EventName::FRONT_END` | The front end |
| `EventName::ADMIN` | The admin pages |
| `EventName::EDITOR` | The editor interface |
| `EventName::BLOCK` | The content of the blocks, front end and editor |
| `EventName::LOGIN` | The login page |

`Hook`, the name of `EventName` before 1.1.0, is deprecated and removed in 2.0.

### Print, check and remove the assets

`ScriptLoader` and `StyleLoader` also work on the handles already registered, by the plugin or by the others.

```php
use ItalyStrap\Asset\AssetStatus;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\ScriptLoader;

$scripts = new ScriptLoader();

// A script registered with RegisterOnly, printed at the start of the head, before the other scripts.
add_action('wp_head', static fn () => $scripts->print(new Handle('my-consent')), 2);

$scripts->is(new Handle('my-consent'), AssetStatus::Done);
$scripts->localize(new Handle('my-plugin'), 'myPlugin', ['restUrl' => rest_url()]);
$scripts->dequeue(new Handle('other-plugin'));
$scripts->deregister(new Handle('other-plugin'));
```

`print()` skips a handle that is not registered, so the caller needs no check. `AssetStatus` has the five statuses of `wp_script_is()` and `wp_style_is()`: `Enqueued`, `Registered`, `Queue`, `ToDo` and `Done`. `StyleLoader` has the same methods, without `localize()`.

### Script modules

A `ScriptModule` is an ES module of the import map, like the view script of a block, loaded by `AssetLoader` with the scripts and the styles.

```php
use ItalyStrap\Asset\AssetLoader;
use ItalyStrap\Asset\Dependencies;
use ItalyStrap\Asset\EventName;
use ItalyStrap\Asset\Handle;
use ItalyStrap\Asset\ScriptModule;
use ItalyStrap\Asset\Translations;

$view = new ScriptModule(
    id: new Handle('@my-plugin/view'),
    source: plugins_url('build/view.js', __FILE__),
    dependencies: new Dependencies('@wordpress/interactivity'),
    translations: new Translations('my-plugin'),
);

add_action(EventName::FRONT_END, static fn () => (new AssetLoader())->load($view));
```

`ScriptModuleLoader` dequeues and deregisters a module by its id.

### The WordPress functions

The package calls the functions of the core without checking them, the code that uses it checks the version of the core when it supports an older one.

| Class | Needs |
| --- | --- |
| `ScriptLoader`, `StyleLoader` | WordPress 6.3, for the strategy of the scripts |
| `ScriptModuleLoader` | WordPress 6.5 |
| `ScriptModule` with `Translations` | WordPress 7.0, for `wp_set_script_module_translations()` |

### The URL of a file

`Core\PluginFileUrl` and `Core\ThemeFileUrl` build the URL from where the file is on the disk, a path or a file of the Finder. `PluginFileUrl` works when the plugin folder is a symlink, `ThemeFileUrl` looks in the child theme and in its parent.

```php
use ItalyStrap\Asset\Core\ThemeFileUrl;

$url = (new ThemeFileUrl())->url(new SplFileInfo(get_stylesheet_directory() . '/build/index.css'));
```

`FolderUrls` does the same for any folder with its URL, without the core.

### Without the core

`ScriptLoader`, `StyleLoader` and `ScriptModuleLoader` call the `ScriptRegistry`, the `StyleRegistry` and the `ScriptModuleRegistry`, `Core\CoreScriptRegistry`, `Core\CoreStyleRegistry` and `Core\CoreScriptModuleRegistry` by default. Pass your own registry to test the code that loads the assets without the core.

## Development

PHP, Composer and the database run in the Docker containers in `.docker/`, the same environment runs in CI. `make help` lists all the targets.

```bash
make up        # Start the containers
make install   # Install the dependencies and build the Codeception actors
make cs        # PHP CodeSniffer, PSR-12 and PHPCompatibility
make stan      # PHPStan at level max
make rector    # Rector dry run, PHP 8.2 and code quality sets, make rector-fix applies it
make unit      # The unit suite, the core is not loaded
make integration # The integration suite, the core is loaded
make infection # Mutation tests on the unit suite, 100% MSI, the adapters in src/Core are tested by the integration suite
make qa        # All the checks above
make down      # Stop and remove the containers
```

The PHP and core versions are in `.docker/test.env`.
