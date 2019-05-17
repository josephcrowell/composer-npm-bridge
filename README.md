# Composer NPM bridge

*NPM integration for Composer packages.*

> This package is a fork from https://github.com/eloquent/composer-npm-bridge (thanks to them!)

## Installation

- Available as [Composer] package [oat-sa/composer-npm-bridge].

[composer]: http://getcomposer.org/
[oat-sa/composer-npm-bridge]: https://packagist.org/packages/oat-sa/composer-npm-bridge

## Requirements

- The `npm` executable must be available in PATH.

## Usage

To utilize the *Composer NPM bridge*, simply add `oat-sa/composer-npm-bridge`
to the `require` section of the project's Composer configuration:

    composer require oat-sa/composer-npm-bridge

NPM dependencies are specified via a [package.json] configuration file in the
root directory of the Composer package. Source control should be configured to
ignore NPM's `node_modules` directory, similar to Composer's `vendor` directory.

[package.json]: https://npmjs.org/doc/json.html

## How does it work?

The *Composer NPM bridge* is a Composer plugin that automatically installs and
updates [NPM] packages whenever the corresponding Composer command is executed.
To detect compatible packages, the bridge inspects Composer package
configuration information to find packages that directly require the
`oat-sa/composer-npm-bridge` Composer package itself.

In addition to normal operation, `composer install` will [install] NPM
dependencies for all Composer packages using the bridge. This includes the root
package, as well as Composer dependencies. 

Similarly, `composer update` will
[install] NPM dependencies for all Composer dependencies using the bridge. It
will also run `npm install` for the NPM dependencies.

NPM dependencies will be installed exactly as if `npm install` were run from the
root directory of the package. This applies even if the package is installed as
a dependency.

[install]: https://npmjs.org/doc/install.html
[npm]: https://npmjs.org/
[update]: https://npmjs.org/doc/update.html

## Configuration

The following configuration can be added to `composer.json` under the
`extra.npm-bridge` section to customize the behavior on a per-package basis.
Values in the root package will not currently impact any dependency packages
that also use *Composer NPM bridge* - each package must define its own options.

Key       | Type  | Default | Description
----------|-------|---------|---------------------------------------------------
timeout   | int   | `300`   | Specify a custom timeout for the installation (in seconds).
optional  | bool  | `false` | Skip instead of throwing an exception if `npm` is not found when processing the package.
arguments | array | []      | Add custom NPM arguments

```json5
{
    // ...

    "extra": {
        "npm-bridge": {
            "timeout": 9000,
            "optional": true,
            "arguments": ["--prefix", "someDir"]
        },

        // ...
    }
}
```

*Composer NPM bridge* can be completely disabled by setting the
`COMPOSER_NPM_BRIDGE_DISABLE` environment variable to a non-empty value:

```shell
COMPOSER_NPM_BRIDGE_DISABLE=1 composer install
```

## Caveats

Because NPM dependencies are installed underneath the root directory of the
Composer package, Composer may complain about working copy changes when the
package is installed as a dependency. Source control should be configured to
ignore the `node_modules` directory in order to avoid this.
