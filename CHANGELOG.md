# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
