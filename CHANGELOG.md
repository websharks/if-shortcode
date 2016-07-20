## v160720.38716

- Updating to latest release of WP Sharks Core.
- Fixed bug in `current_user_can_download=""` and `current_user_bought_product=""` attributes that triggering syntax errors in some rare scenarios.
- Fixed bug that was causing an `E_NOTICE` level warning about `$limit`.
- Fixed bug causing a PHP warning in some scenarios related to `s::setTransient()`.
- Fixed bug that was causing `current_user_can_download=""` to fail when checking for a download sold by a product variation.

## v160720.23954

- Deepening WooCommerce integration. New attribute `current_user_can_download=""`.
- Updating inline documentation. References to `debug=""` should be `_debug=""`.
- Updating inline documentation. References to `for_blog=""` should be `_for_blog=""`.

## v160718.59682

- Adding options page with general settings.
- Improving colored error output when `debug="true"`.

## v160715.31825

- Updating to latest websharks/core with Simple Expression bug fixes that improve this plugin.

## v160714.41537

- Bug fix. Loose types for `onShortcode()` handler.

## v160713.41453

- Updating to the latest WP Sharks Core.
- Taking advantage of recent improvements in core that further optimize this plugin.

## v160709.39379

- Updating to latest WPSC and websharks/core.
- Refactor. Now using Simple Expression syntax from websharks/core.
- Adding support for a new `_debug=""` attribute.
- Adding support for arbitrary attributes.

## v160707.2545

- Updating to latest phings build system.
- Fixing bug in initial release related to a corrupt build.

## v160707.450

- Initial release.
