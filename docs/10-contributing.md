# Contributing Guide

Thank you for your interest in contributing to Akira Laravel PDF Invoices!

## Code of Conduct

Be respectful, inclusive, and professional in all interactions.

## Getting Started

### Fork and Clone

```bash
git clone https://github.com/YOUR-USERNAME/laravel-pdf-invoices.git
cd laravel-pdf-invoices
```

### Setup Environment

```bash
composer install
npm install
npm run dev
```

### Run Tests

```bash
composer test
```

### Code Quality

```bash
composer analyse     # PHPStan
composer format      # Laravel Pint
npm run build        # Tailwind CSS
```

## Making Changes

### Branch Naming

Use semantic branch names:

- `feat/feature-name` - New feature
- `fix/bug-description` - Bug fix
- `docs/documentation-update` - Documentation
- `refactor/improvement` - Code refactoring
- `test/test-additions` - Test additions
- `chore/maintenance` - Maintenance tasks

### Commit Messages

Follow conventional commits:

```
feat: add invoice generation feature
fix: resolve currency formatting issue
docs: update usage documentation
refactor: improve builder pattern
test: add invoice calculation tests
chore: update dependencies
```

### Code Standards

- PHP 8.4 syntax
- Strict types (`declare(strict_types=1)`)
- PSR-12 coding standards
- PHPStan level max
- No inline comments (PHPDoc only)
- Laravel best practices

### Writing Tests

Add tests for new features:

```php
describe('New Feature', function () {
    it('does something important', function () {
        $result = someFunction();

        expect($result)->toBe('expected');
    });
});
```

Run tests before submitting:

```bash
composer test
```

### Documentation

Update documentation for:

- New features
- API changes
- Configuration options
- Breaking changes

Add examples and clear explanations.

## Pull Request Process

1. **Create Feature Branch**

   ```bash
   git checkout -b feat/my-feature
   ```

2. **Make Changes**

- Write code following standards
- Add tests
- Update documentation

3. **Run Quality Checks**

   ```bash
   composer analyse
   composer format
   composer test
   npm run build
   ```

4. **Commit Changes**

   ```bash
   git add .
   git commit -m "feat: add my feature"
   ```

5. **Push and Create PR**

   ```bash
   git push origin feat/my-feature
   ```

6. **Open Pull Request**

- Clear title and description
- Reference issues (Closes #123)
- Explain changes and reasoning

### PR Description Template

```markdown
## Description

Brief description of changes.

## Related Issues

Closes #123

## Changes Made

- Change 1
- Change 2
- Change 3

## Testing

How to test these changes.

## Checklist

- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] Code passes quality checks
- [ ] Commits are well-structured
```

## Code Review

PRs will be reviewed for:

- Code quality and standards
- Test coverage
- Documentation completeness
- SOLID principles
- Performance impact
- Security considerations

## Areas for Contribution

### Priority Areas

- **Template Improvements**: New templates or enhance existing ones
- **Documentation**: Expand guides and examples
- **Tests**: Improve test coverage
- **Performance**: Optimize calculations and rendering
- **Features**: New functionality (with prior discussion)
- **Bug Fixes**: Report and fix issues

### Documentation

- Improve existing docs
- Add examples
- Fix typos
- Add translations (future)

### Testing

- Add feature tests
- Add edge case tests
- Improve test coverage

### Templates

- Create new templates
- Improve existing templates
- Add template variations

## Release Process

The maintainers handle releases using release-it:

```bash
npm run release
```

This:

- Updates version in package.json
- Generates CHANGELOG
- Creates git tag
- Publishes to GitHub

## Questions?

- Open an issue for discussions
- Check existing documentation
- Review other PRs for examples

## License

By contributing, you agree that your contributions are licensed under the MIT License.

## Recognition

Contributors are recognized in:

- README.md
- CHANGELOG.md
- GitHub contributors page

Thank you for contributing!---

---

**← Previous:** [09 - CSS Compilation](./09-css-compilation.md)
