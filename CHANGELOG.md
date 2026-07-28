# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-07-28

### Fixed

- External stylesheets were rendered without an `href` attribute, producing a `<link>` tag that never
  loaded the CSS file. The URL is now always assigned, after the caller-supplied attributes, so it
  cannot be dropped or overridden — matching how `src` is handled for external scripts.

### Changed

- Corrected `@return` PHPDoc on `renderExternalScript()` and `renderExternalStyle()`, which claimed the
  methods return an empty string while they actually return the rendered tag.

## [1.0.0] - 2026-02-04

### Added

- Initial release of Hryvinskyi_ThemeAssets module
- `AssetRendererInterface` for rendering scripts and stylesheets
  - Support for inline and external rendering
  - Script loading strategies: sync, async, defer
  - Resource preloading with fetch priority options
  - Custom HTML attributes support
  - Content prepend/append for inline scripts
- `AssetPathResolverInterface` for resolving asset file paths
  - File path resolution
  - File content retrieval
  - Asset caching
- `AssetContentStrategyInterface` for asset content retrieval strategies
- `AssetContentStrategyPoolInterface` for managing multiple content strategies
- Three built-in content retrieval strategies:
  - `MagentoAssetSystemStrategy` - Magento's native asset system
  - `DirectStaticFileStrategy` - Direct static file access
  - `FilesystemServiceStrategy` - Filesystem service fallback
- Custom logging to `var/log/asset_renderer.log`
- Full PHPDoc documentation for all public methods
- Compatibility with PHP 8.1, 8.2, and 8.3

### Notes

- This module was extracted from `MageCloud_ThemeBlank` for better modularity
- Backward compatibility is maintained through DI preferences in the original module
