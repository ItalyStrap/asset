# Deep code review and modernization notes

This repository already has a useful separation between asset registration (`src/Asset.php`, `src/Script.php`, `src/Style.php`), configuration parsing (`src/ConfigBuilder.php`), and orchestration (`src/AssetManager.php`, `src/AssetsSubscriber.php`). The review below focuses on the most relevant improvements for maintainability and modernization while keeping compatibility with the current PHP 7.2 baseline.

## What is working well

- `declare(strict_types=1);` is already used in most concrete classes.
- The public API is relatively small and easy to understand.
- Unit tests cover the main behavior for asset registration, enqueueing, configuration parsing, and version handling.
- Static-analysis and QA tooling are already present in the repository configuration, even if some of it now needs modernization.

## Highest-priority improvements

### 1. Modernize the development toolchain

**Why this matters**

The current development dependencies are pinned to versions that do not install on PHP 8.3. In the current dependency set, a Composer install also rejects the `lucatume/wp-browser` chain because the allowed `symfony/process` versions are flagged by security advisories. This makes the repository hard to validate on a modern runtime.

**Evidence**

- `composer.json` requires old dev tools such as:
  - `dealerdirect/phpcodesniffer-composer-installer:^0.5.0`
  - `szepeviktor/phpstan-wordpress:^0.3.0`
  - `infection/infection:^0.15.3`
  - `lucatume/wp-browser:^2.4`
- Installing dependencies on PHP 8.3 currently fails because those constraints target older PHP versions and older Symfony components.

**Recommendation**

- Refresh the `require-dev` stack to PHP-8-compatible versions in a dedicated follow-up PR.
- Rebuild `composer.lock` after upgrading tooling.
- Revisit whether all current QA tools are still required, or whether some can be replaced with fewer, better-supported tools.

### 2. Fix and simplify Composer automation

**Why this matters**

The Composer metadata and scripts should be the first entry point for contributors, but they currently contain avoidable friction.

**Evidence**

- The `support` URLs in `composer.json` pointed to `ItalyStrap/asses` instead of `ItalyStrap/asset`.
- The `test` script currently maps to a bare `test` shell command instead of the project test suite.
- Several scripts use Windows-style `vendor\\bin\\...` separators, which makes them less portable.

**Recommendation**

- Keep Composer metadata accurate.
- Replace the placeholder `test` script with an actual project test entry point.
- Normalize Composer scripts to use forward slashes for cross-platform compatibility.

### 3. Tighten typing in interfaces and config parsing

**Why this matters**

The codebase already uses strict types, but some interfaces and internal methods still rely on loose or undocumented value shapes. That makes static analysis less effective.

**Evidence**

- `src/FileInterface.php` returns `mixed` from `version()`.
- `src/Version/VersionInterface.php` also returns a broad `mixed|string|bool|null`.
- `src/ConfigBuilder.php` contains loosely typed internals such as `generateFileUrl()` and `getFileInfo()`.

**Recommendation**

- Narrow return types and parameter types where PHP 7.2 allows it.
- Improve docblocks for array shapes in `ConfigBuilder`.
- Consider introducing small value objects for parsed asset definitions in a future major version.

### 4. Reduce legacy callable patterns

**Why this matters**

The codebase still contains older callable invocation patterns that can be simplified without changing behavior.

**Evidence**

- `src/Asset.php` used `call_user_func()` to evaluate `load_on`.

**Recommendation**

- Prefer direct callable invocation where the value is already known to be callable.
- Apply the same cleanup to similar legacy patterns in future changes.

### 5. Improve README and contributor guidance

**Why this matters**

The root `README.md` is currently too short to help new contributors understand the package, expected config format, or verification workflow.

**Evidence**

- The current README only contains the package name, one sentence, and the alpha version.

**Recommendation**

- Expand the README with installation, basic usage, configuration examples, and development commands.
- Add a short contributor section that explains the intended validation flow.

## Medium-priority improvements

- Replace `get_class($finder)` with `$finder::class` in `src/ConfigBuilder.php`.
- Audit exception messages and naming for grammar/consistency, for example `"has already been registered"`.
- Review whether `AssetFactory` should validate class names before instantiation to produce clearer errors.
- Add more targeted tests around invalid configuration shapes and missing file resolution paths.
- Remove stale code and examples from `tests/_temp/` if they are no longer used.

## Small improvements applied in this change set

- Replaced the legacy `call_user_func()` usage in `src/Asset.php` with direct callable invocation.
- Added a focused unit test to confirm the load-condition callable is invoked exactly once.
- Fixed the broken Composer support URLs.
- Added this review document and linked it from the README so future contributors can find the recommendations in-repo.

## Suggested follow-up plan

1. Upgrade `require-dev` dependencies to versions that support modern PHP.
2. Restore a real `composer test` pipeline that works cross-platform.
3. Tighten interface and internal typing while staying compatible with supported PHP versions.
4. Expand the README with usage and contributor documentation.
5. Re-run static analysis and tests after each modernization step to keep the package stable.
