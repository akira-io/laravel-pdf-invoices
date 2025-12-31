# Roadmap

This document outlines planned features and enhancements for Laravel PDF Invoices. All items are feature-oriented without specific dates or versions.

## Architecture & Core

### Database Persistence Layer
Add optional Eloquent model support for storing invoice records in the database. The existing builder pattern and DTO structure provide a clean foundation for hydration and persistence.

**Extension Points:**
- Migration stub at `database/migrations/create_pdf_invoices_table.php.stub` is already scaffolded
- Readonly DTOs can easily serialize to/from database columns
- Custom attributes system supports dynamic fields without schema changes

### Multi-Currency Support
Enhance currency handling beyond formatting to include conversion rates and multi-currency invoicing.

**Extension Points:**
- `CurrencyFormatterContract` can be extended with rate conversion methods
- `InvoiceData` currency property already exists
- ConfigManager provides centralized currency configuration

### Event System
Introduce Laravel events for key invoice lifecycle points: created, generated, stored, failed.

**Extension Points:**
- Service Provider can dispatch events during PDF generation
- Storage operations are isolated in `StorageDriverContract`
- PDF generation is abstracted via `PdfGeneratorContract`

## Templates & Presentation

### Additional Templates
Expand beyond the three existing templates (minimal, modern, branded) with industry-specific designs.

**Extension Points:**
- Template views in `resources/views/pdf/templates/`
- CSS compilation system already established in `resources/css/`
- Template selection via `config('pdf-invoices.pdf.template')`

### Dynamic Template Engine
Allow runtime template switching and programmatic template composition.

**Extension Points:**
- `PdfGeneratorContract::generate()` accepts template parameter
- Blade views can be composed dynamically
- InvoiceTranslator provides localization hooks

### Watermark Support
Add configurable watermarks (paid, draft, void) to generated PDFs.

**Extension Points:**
- Both `SpatiePdfGenerator` and `DompdfPdfGenerator` support custom rendering
- View composition can inject watermark layers
- Custom attributes on `InvoiceData` can control watermark display

## Internationalization

### Additional Locales
Expand beyond English, Portuguese, and French to support more languages.

**Extension Points:**
- Translation files in `resources/lang/{locale}/invoice.php`
- `InvoiceTranslator` class handles locale-aware translations
- Supported locales configurable via `config('pdf-invoices.localization.supported_locales')`

### RTL Language Support
Add right-to-left layout support for Arabic, Hebrew, and other RTL languages.

**Extension Points:**
- Template blade files can detect locale and apply RTL classes
- CSS system supports directional styling
- InvoiceTranslator can expose `isRtl()` helper

## Storage & Distribution

### Cloud Storage Drivers
Extend storage beyond local filesystem to S3, Azure, GCS via dedicated drivers.

**Extension Points:**
- `StorageDriverContract` defines storage interface
- Laravel's filesystem abstraction already supports cloud disks
- ConfigManager controls disk selection

### Email Integration
Add direct email delivery of generated invoices.

**Extension Points:**
- Generated PDF strings can attach to Laravel Mail
- `PdfGeneratorContract::generate()` returns PDF content
- Mailable classes can consume `InvoiceData`

### Batch Generation
Support generating multiple invoices in a single operation with queueing.

**Extension Points:**
- Builders are immutable and queue-safe
- Storage and generation are separated concerns
- Laravel Queue can process invoice generation jobs

## Developer Experience

### Artisan Commands
Create commands for common operations: template scaffolding, invoice generation, cache clearing.

**Extension Points:**
- `PdfInvoicesCommand` stub exists at `src/Commands/`
- Service Provider can register commands
- Builders provide programmatic API for command usage

### API Resources
Provide JSON:API or REST resources for invoice data serialization.

**Extension Points:**
- Readonly DTOs can easily map to API resources
- Calculation methods on `InvoiceData` expose totals
- Custom attributes support arbitrary response fields

### Testing Utilities
Add test helpers and factories for rapid test fixture creation.

**Extension Points:**
- Builders simplify test data creation
- Factory stubs exist at `database/factories/`
- Pest architecture tests demonstrate testing patterns

## Calculations & Business Logic

### Compound Tax Support
Enable multiple simultaneous tax rates per line item.

**Extension Points:**
- `ItemData` attributes array supports additional tax metadata
- Calculation methods in `ItemData` can be extended
- Custom formatters can display multiple tax breakdowns

### Payment Tracking
Track partial payments and outstanding balances.

**Extension Points:**
- Custom attributes on `InvoiceData` can store payment records
- Calculation methods can compute balances
- Database persistence layer can store payment history

### Recurring Invoices
Generate invoices automatically on schedules.

**Extension Points:**
- Builders can be serialized and stored for replay
- Laravel Scheduler can trigger generation
- InvoiceData immutability ensures consistency

**Next:** [Installation](01-installation.md)
