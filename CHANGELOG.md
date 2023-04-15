# Changelog

## [v1.0.0-alpha5](https://github.com/aphiria/app/compare/v1.0.0-alpha4...v1.0.0-alpha5) (?)

### Changed

- Updated PHPUnit and Psalm ([#33](https://github.com/aphiria/app/pull/33))

## [v1.0.0-alpha4](https://github.com/aphiria/app/compare/v1.0.0-alpha3...v1.0.0-alpha4) (2022-12-10)

### Changed

- Updated to require PHP 8.2 ([#31](https://github.com/aphiria/app/pull/31))

## [v1.0.0-alpha3](https://github.com/aphiria/app/compare/v1.0.0-alpha2...v1.0.0-alpha3) (2022-11-27)

### Changed

- Updated to use Aphiria's new app builders in _index.php_ and _aphiria_ to simplify supporting of asynchronous runtimes such as Swoole and ReactPHP ([#30](https://github.com/aphiria/app/pull/30))
  - Updated `IntegrationTestCase` to return an `IApplication` instance ([#30](https://github.com/aphiria/app/pull/30))
- Updated to use Aphiria's auth library ([#27](https://github.com/aphiria/app/pull/27))
- Renamed `App\App` to `App\GlobalModule` ([#21](https://github.com/aphiria/app/pull/21))
- Renamed all `IModule::build()` methods to `configure()` ([#21](https://github.com/aphiria/app/pull/21))
- Updated Composer scripts to not run `php aphiria framework:flushcaches` anymore after `composer install` and `composer update` ([#18](https://github.com/aphiria/app/pull/18))
- Updated Psalm to 4.10 ([#19](https://github.com/aphiria/app/pull/19))
- Updated PHP-CS-Fixer to 3.2 ([#19](https://github.com/aphiria/app/pull/19))

### Added

- Added `APP_BUILDER_API` and `APP_BUILDER_CONSOLE` environment variables to _.env.dist_ ([#30](https://github.com/aphiria/app/pull/30))

## [v1.0.0-alpha2](https://github.com/aphiria/app/compare/v1.0.0-alpha1...v1.0.0-alpha2) (2021-2-15)

### Changed

- Updated default port number to 8080 ([#15](https://github.com/aphiria/app/pull/15))
- Reintroduced PHP-CS-Fixer and ran it ([#11](https://github.com/aphiria/app/pull/11))
- Updated Psalm, added checks for unused suppressions ([#13](https://github.com/aphiria/app/pull/13))
- Updated copyright year

## v1.0.0-alpha1 (2020-12-20)

### Added

- Literally everything
