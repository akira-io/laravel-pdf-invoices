# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


# [1.8.0](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.7.0...v1.8.0) (2026-05-11)


### Features

* add Laravel 13 support ([5c05736](https://github.com/akira-io/laravel-pdf-invoices/commit/5c057367f4f331b20b5281ac16ae3fbccdff140f))

# [1.7.0](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.6.0...v1.7.0) (2026-02-12)


### Features

* Add new feature tests, enhance builders with bulk assignment, and improve robustness for config and locale handling. ([344a7dc](https://github.com/akira-io/laravel-pdf-invoices/commit/344a7dcf321077d5804f7d2c643e322e984f13d8))
* enhance Spatie PDF generator to gracefully handle invalid base64 content and add a test for decoding failures. ([a76acab](https://github.com/akira-io/laravel-pdf-invoices/commit/a76acab284534c053b6240a604d453873fa29476))
* explicitly use `browsershot` driver in `SpatiePdfGenerator` and update related documentation and tests. ([f7fd489](https://github.com/akira-io/laravel-pdf-invoices/commit/f7fd489bf745ba3e6ae37af63ea0142e8e10aa66))
* Refactor currency formatting logic, update test coverage expectation, and add a test for the Spatie PDF generator's real driver branch. ([68a4609](https://github.com/akira-io/laravel-pdf-invoices/commit/68a4609166c04fc03abc42e1ee53ae6ee24faa29))

## [1.6.0](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.5.0...v1.6.0) (2026-02-08)


### Features

* add Italian localization for invoice labels ([70e5c3b](https://github.com/akira-io/laravel-pdf-invoices/commit/70e5c3b332df98d6563a35bb0576d2d4bd85a443))
* add Spanish localization for invoice labels ([009c6d4](https://github.com/akira-io/laravel-pdf-invoices/commit/009c6d4dbd59a33ed82b7d759781903da64c5491))
* update Portuguese localization for invoice labels ([54b730d](https://github.com/akira-io/laravel-pdf-invoices/commit/54b730d62098a7826ac4b4a6714c3ee330ad512d))

## [1.5.0](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.4.2...v1.5.0) (2026-02-08)


### Features

* add Portuguese localization for invoice labels ([2c85473](https://github.com/akira-io/laravel-pdf-invoices/commit/2c8547328f482f975f4d4eaabecbf09b78e37aa8))

## [1.4.2](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.4.1...v1.4.2) (2025-12-04)


### Bug Fixes

* fixed 'page' label localization for invoices in English, French, and Spanish ([debe5b9](https://github.com/akira-io/laravel-pdf-invoices/commit/debe5b94a4f040c6bb14b6e62753a99ebbe90f0c))

## [1.4.1](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.4.0...v1.4.1) (2025-12-04)


### Bug Fixes

* fixed locale handling in PDF generators to support invoice-specific locales ([84416ec](https://github.com/akira-io/laravel-pdf-invoices/commit/84416ecaefff89f36d9ae986b018014852cb98e0))

## [1.4.0](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.3.0...v1.4.0) (2025-12-04)


### Features

* add locale support to invoice builder tests ([b13a133](https://github.com/akira-io/laravel-pdf-invoices/commit/b13a1335e5afd5e976c27bf2e380a59927ccd833))
* enhance invoice functionality with locale support and refactor currency handling ([b5c9e13](https://github.com/akira-io/laravel-pdf-invoices/commit/b5c9e13cacfcb237aa261b81c0b2875433e64a57))

## [1.3.0](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.2.0...v1.3.0) (2025-12-04)


### Features

* add French to supported locales for invoice localization ([cba3d95](https://github.com/akira-io/laravel-pdf-invoices/commit/cba3d95e1a469a7161e1a4b013bec1813e8ba2af))
* add French translations for invoice-related terms ([d7368cf](https://github.com/akira-io/laravel-pdf-invoices/commit/d7368cfbb4b0404dcb5d89984e44e01b66469173))


## [1.2.0](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.1.2...v1.2.0) (2025-12-02)

### Features

* add ConfigManager for type-safe configuration access and fix PHPStan level max
  issues ([4bc806a](https://github.com/akira-io/laravel-pdf-invoices/commit/4bc806a363042f2e713955f6f986546ec9b12195))
* add puppeteer as a dependency in
  package.json ([653c865](https://github.com/akira-io/laravel-pdf-invoices/commit/653c865a06686010649cb4826355bcf71878ec8b))
* add support for DomPDF as an alternative PDF generator and enhance configuration
  options ([5f213bc](https://github.com/akira-io/laravel-pdf-invoices/commit/5f213bca6f2106654932a3f7a8de692e74baf46d))
* enhance CSS retrieval by integrating Vite manifest
  support ([42fac19](https://github.com/akira-io/laravel-pdf-invoices/commit/42fac19f21111154154ed5e43fb528cf1d47b82d))
* enhance seller information display in invoice
  templates ([d82d752](https://github.com/akira-io/laravel-pdf-invoices/commit/d82d752741d0914df55ac3c7490607a1d95c8767))

### Bug Fixes

* remove addUnreleased from release-it config to properly include
  header ([1a6d03e](https://github.com/akira-io/laravel-pdf-invoices/commit/1a6d03e88cd7e15076a753b4987ce9c20e04e3c2))
* resolve all PHPStan level max issues with proper type handling and
  assertions ([901a92d](https://github.com/akira-io/laravel-pdf-invoices/commit/901a92d534faf8dc6d8fe7d3170e92f8e004a4c5))
* update issuedAt and dueAt methods to support Carbon and DateTime interfaces in
  documentation ([e7030db](https://github.com/akira-io/laravel-pdf-invoices/commit/e7030db11f04efc07eb2cd0edbaea8d6582367ad))

## [1.1.2](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.1.1...v1.1.2) (2025-11-04)

### Bug Fixes

* update issuedAt and dueAt parameters to support Carbon and DateTime interfaces in
  InvoiceData ([869eac3](https://github.com/akira-io/laravel-pdf-invoices/commit/869eac35998e119f1382227dce5552a790fc2047))

## [1.1.1](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.1.0...v1.1.1) (2025-11-04)

### Bug Fixes

* reorder type hints for issuedAt and dueAt methods in
  InvoiceBuilder ([84c9426](https://github.com/akira-io/laravel-pdf-invoices/commit/84c94263988f0f3afdeaea274ac127de0c4ec00e))

## [1.1.0](https://github.com/akira-io/laravel-pdf-invoices/compare/v1.0.0...v1.1.0) (2025-11-04)

### Features

* replace DateTime with Carbon for date handling in
  InvoiceBuilder ([2bc171a](https://github.com/akira-io/laravel-pdf-invoices/commit/2bc171a649347b0e245ea1f68aab416f3e1d32a9))

## 1.0.0 (2025-10-27)

### Features

* add invoice generation functionality with builders and data transfer objects; update
  dependencies ([3e78997](https://github.com/akira-io/laravel-pdf-invoices/commit/3e789974e1c3253b424669372a65033262dadadf))
* enhance invoice templates with improved layout and styling for better
  readability ([0602ebe](https://github.com/akira-io/laravel-pdf-invoices/commit/0602ebe9609cf9afcaba3449d6fb9489080c87bc))
* enhance PHPStan configuration and add custom attributes documentation; introduce builder pattern usage
  examples ([720939a](https://github.com/akira-io/laravel-pdf-invoices/commit/720939aabbd52592658a511c44a9d33fe2a5146e))
* implement invoice builder and entity builder with comprehensive tests; add branded and modern invoice
  templates ([6273b25](https://github.com/akira-io/laravel-pdf-invoices/commit/6273b25623dbe84c4b573a02aaadb4ba754ff86e))

### Bug Fixes

* update package name in composer.json and enhance README with new features and installation
  instructions ([4b3920e](https://github.com/akira-io/laravel-pdf-invoices/commit/4b3920ea83afb6e7d2de276c09684898f8db2d10))
