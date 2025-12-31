# Contributing

Thank you for considering contributing to Laravel PDF Invoices! This document outlines the contribution process and guidelines.

## Code of Conduct

This project adheres to the Laravel Community Code of Conduct. By participating, you are expected to uphold this code.

## How to Contribute

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates.

**When reporting bugs, include:**

- Laravel version
- PHP version
- Package version
- Operating system
- PDF driver being used (Spatie or DomPDF)
- Complete error message and stack trace
- Minimal code to reproduce the issue

**Use this template:**

```markdown
**Environment:**
- Laravel: 12.0
- PHP: 8.4
- Package: 1.0.0
- OS: Ubuntu 22.04
- PDF Driver: spatie

**Description:**
Clear description of the bug.

**Steps to Reproduce:**
1. Step one
2. Step two
3. Step three

**Expected Behavior:**
What should happen.

**Actual Behavior:**
What actually happens.

**Code Sample:**
```php
// Minimal reproducible code
```

**Error Message:**
```
Full error message and stack trace
```
```

### Suggesting Features

Feature suggestions are welcome! Please:

- Check the roadmap in `docs/00-roadmap.md`
- Search existing feature requests
- Provide clear use cases and benefits
- Consider backward compatibility
- Suggest implementation approach if possible

### Pull Requests

**Before submitting a pull request:**

1. Fork the repository
2. Create a feature branch from `main`
3. Write code following our standards
4. Add tests for new functionality
5. Update documentation if needed
6. Ensure all tests pass
7. Update CHANGELOG.md

**Pull request process:**

1. Ensure CI passes (tests, linting, static analysis)
2. Request review from maintainers
3. Address review feedback
4. Wait for approval and merge

## Development Setup

### Installation

Clone your fork and install dependencies:

```bash
git clone https://github.com/your-username/laravel-pdf-invoices.git
cd laravel-pdf-invoices
composer install
npm install puppeteer
```

### Running Tests

Run the full test suite:

```bash
composer test
```

Run specific test types:

```bash
composer test:lint      # Laravel Pint
composer test:refactor  # Rector
composer test:typos     # Peck
composer test:arch      # Architecture tests
composer test:types     # PHPStan
composer test:coverage  # Code coverage
```

### Code Quality

**Linting:**

```bash
composer lint
# or check without fixing
composer test:lint
```

**Static Analysis:**

```bash
composer test:types
```

**Refactoring:**

```bash
composer refactor
# or check without fixing
composer test:refactor
```

## Coding Standards

### PHP Standards

- Follow PSR-12 coding standard
- Use strict types: `declare(strict_types=1);`
- Use type hints for all parameters and return types
- Use readonly properties where appropriate
- Use named arguments for clarity
- Avoid magic methods and properties

**Example:**

```php
<?php

declare(strict_types=1);

namespace Akira\PdfInvoices\Example;

final readonly class ExampleClass
{
    public function __construct(
        private string $name,
        private int $count,
    ) {}

    public function process(string $input): string
    {
        // Implementation
        return $input;
    }
}
```

### Documentation Standards

- Add PHPDoc blocks for classes and complex methods
- Document parameter types and return types
- Include `@throws` tags for exceptions
- Keep comments concise and meaningful
- Update documentation files when changing public APIs

**Example:**

```php
/**
 * Generate a PDF invoice from invoice data.
 *
 * @param  InvoiceData  $invoice  The invoice data to generate PDF from
 * @param  string  $template  The template name to use for rendering
 * @return string The generated PDF content as binary string
 * @throws RuntimeException If PDF generation fails
 */
public function generate(InvoiceData $invoice, string $template = 'modern'): string
{
    // Implementation
}
```

### Testing Standards

- Write tests for all new features
- Maintain or improve code coverage
- Use descriptive test names
- Follow Pest PHP conventions
- Test edge cases and error conditions

**Example:**

```php
test('builds invoice with all required fields', function () {
    $invoice = InvoiceBuilder::make()
        ->seller(EntityBuilder::make()->name('Seller')->build())
        ->buyer(EntityBuilder::make()->name('Buyer')->build())
        ->addItem(ItemBuilder::make()->description('Item')->unitPrice(100)->build())
        ->build();
    
    expect($invoice)
        ->toBeInstanceOf(InvoiceData::class)
        ->seller->toBeInstanceOf(EntityData::class)
        ->buyer->toBeInstanceOf(EntityData::class);
});

test('throws exception when seller is missing', function () {
    InvoiceBuilder::make()
        ->buyer(EntityBuilder::make()->name('Buyer')->build())
        ->build();
})->throws(InvalidArgumentException::class, 'Seller is required.');
```

## Commit Message Guidelines

Use conventional commit format:

```
type(scope): subject

body

footer
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Test additions or changes
- `chore`: Build process or tool changes

**Examples:**

```
feat(builder): add support for payment terms

Add paymentTerms() method to InvoiceBuilder to specify
payment terms and conditions.

Closes #123
```

```
fix(pdf): resolve DomPDF memory leak on large invoices

Optimize image loading and CSS parsing to reduce memory
consumption when generating PDFs with many items.

Fixes #456
```

## Branch Naming

Use descriptive branch names:

- `feature/add-payment-terms`
- `fix/dompdf-memory-leak`
- `docs/update-installation-guide`
- `refactor/optimize-calculations`

## Changelog

Update `CHANGELOG.md` with your changes:

```markdown
## [Unreleased]

### Added
- Support for payment terms in invoice builder

### Fixed
- DomPDF memory leak on large invoices

### Changed
- Improved error messages for missing required fields
```

## Documentation

Update relevant documentation when making changes:

- API changes: Update method signatures in docs
- New features: Add new documentation sections
- Configuration: Update config documentation
- Examples: Add practical examples for new features

## Questions?

- Open a discussion on GitHub
- Tag maintainers in pull requests
- Check existing issues and discussions

Thank you for contributing to Laravel PDF Invoices!
