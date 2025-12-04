# CSS Compilation and Template Styling

## Overview

The Laravel PDF Invoices package uses compiled Tailwind CSS that is injected directly into Blade templates during PDF
generation. This ensures that all styling is properly rendered in the generated PDF files.

## Architecture

### CSS Compilation Flow

1. **Source Files**: The package includes Tailwind utility classes in templates
2. **Host Application**: The consuming Laravel application compiles Tailwind CSS using Vite
3. **Compiled CSS**: Generated CSS file is placed at `resources/css/compiled.css`
4. **PDF Generation**: The `SpatiePdfGenerator` reads the compiled CSS and injects it into templates

### Why This Approach?

- **Automatic Theming**: Uses the host application's Tailwind theme and configuration
- **No Duplication**: Single source of truth for styling
- **Consistent Styling**: Ensures PDF styling matches the application's design system
- **Easy Customization**: Users can extend or modify the theme in their own Tailwind config

## Compilation Setup

### In the Host Application

The package expects Tailwind CSS to be compiled by the consuming application. This is typically already configured in
Laravel 12+ applications.

#### 1. Update `app.css` to include invoice templates

Add the following `@source` directive to your application's `resources/css/app.css`:

```css
@import 'tailwindcss';

/* Include invoice template source files for Tailwind compilation */
@source '../../Packages/Laravel/laravel-pdf-invoices/resources/views/**/*.blade.php';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}
```

#### 2. Build the CSS

Run the Vite build command to compile Tailwind CSS:

```bash
npm run build
```

This generates the compiled CSS file that will be used by the PDF generator.

#### 3. Locate the compiled file

The compiled CSS will be generated at:

```
public/build/assets/app-[hash].css
```

## How Templates Use CSS

### Template Structure

Each invoice template includes the compiled CSS in the `<style>` tag:

```blade
<head>
    <style>
        {!! $compiledCss !!}
    </style>
</head>
```

### CSS Injection

The `SpatiePdfGenerator` reads the compiled CSS and passes it to templates:

```php
private function getCompiledCss(): string
{
    $cssPath = __DIR__ . '/../../resources/css/compiled.css';

    if (!file_exists($cssPath)) {
        return '';
    }

    return file_get_contents($cssPath);
}
```

## Tailwind Classes Used

The invoice templates use the following Tailwind CSS utilities:

### Layout

- `w-full` - Full width layout
- `max-w-*` - Max-width constraints
- `px-*`, `py-*` - Padding utilities
- `mb-*`, `mt-*` - Margin utilities
- `flex`, `grid` - Layout modes
- `gap-*` - Gap between items

### Typography

- `text-*` - Font sizes (xs, sm, base, lg, xl, 2xl, 3xl, 4xl, 6xl)
- `font-bold`, `font-semibold`, `font-light` - Font weights
- `text-gray-*` - Text colors
- `uppercase` - Text transform
- `leading-*` - Line height

### Colors & Styling

- `bg-white`, `bg-gray-*` - Background colors
- `border-*` - Border utilities
- `shadow-*` - Shadow effects
- `rounded` - Border radius

### Responsive

Templates are optimized for PDF rendering and use absolute sizing (no breakpoints).

## Customization

### Changing the Theme

Modify your application's `resources/css/app.css` to customize the theme:

```css
@import 'tailwindcss';

@theme {
    --color-primary: #your-color;
    --font-sans: 'Your Font Family';
}
```

### Adding Custom Utilities

Add custom utilities in `resources/css/app.css`:

```css
@layer utilities {
    .invoice-header {
        @apply px-12 py-8 border-b-4 border-blue-600;
    }
}
```

### Override Template Styles

Extend invoice templates by modifying the Blade files in your resources directory or by creating a service provider that
publishes the package views.

## CSS File Size

The compiled CSS file typically ranges from 40-50 KB (minified). This is embedded directly in each PDF, making
individual PDF files larger than with external CSS references, but ensuring complete styling independence.

### Size Optimization

To minimize the compiled CSS size:

1. Use Tailwind's `@apply` directive in custom CSS instead of inline classes
2. Limit the number of custom color variants
3. Use Tailwind's built-in color palette when possible

## Troubleshooting

### Styles not appearing in PDFs

1. **Verify CSS file exists**: Check if `resources/css/compiled.css` exists in the package
2. **Rebuild CSS**: Run `npm run build` in your application
3. **Check Tailwind config**: Ensure your `tailwind.config.js` or theme configuration is correct
4. **Verify @source paths**: Make sure the `@source` directive in `app.css` points to the correct package location

### Larger PDF file sizes

This is expected as the entire compiled CSS (40-50 KB) is embedded in each PDF. This trade-off ensures:

- Complete styling independence
- No external dependencies during PDF viewing
- Consistent rendering across all systems

## Future Improvements

Potential optimizations being considered:

1. **CSS Purging**: Remove unused styles from each template
2. **Template-specific CSS**: Generate separate CSS files per template
3. **Lazy Loading**: Only include CSS for needed components
4. **External CSS Support**: Option to reference external CSS URLs

## References

- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Vite CSS Processing](https://vitejs.dev/guide/features.html#css)
- [Spatie Laravel PDF](https://spatie.be/docs/laravel-pdf)---

---

**← Previous:** [08 - Localization](./08-localization.md) | **Next:** [10 - Contributing →](./10-contributing.md)
