# Contributing to Professional Portfolio

First off, thank you for considering contributing to this project! 🎉

The following is a set of guidelines for contributing to this portfolio website. These are mostly guidelines, not rules. Use your best judgment, and feel free to propose changes to this document in a pull request.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)

---

## 📜 Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to eliasaraya142@gmail.com.

### Our Standards

- ✅ Using welcoming and inclusive language
- ✅ Being respectful of differing viewpoints and experiences
- ✅ Gracefully accepting constructive criticism
- ✅ Focusing on what is best for the community
- ✅ Showing empathy towards other community members

---

## 🤝 How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

**Bug Report Template:**
```markdown
**Describe the bug**
A clear and concise description of what the bug is.

**To Reproduce**
Steps to reproduce the behavior:
1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

**Expected behavior**
A clear description of what you expected to happen.

**Screenshots**
If applicable, add screenshots to help explain your problem.

**Environment:**
 - OS: [e.g. Windows 10]
 - Browser: [e.g. Chrome 90]
 - Version: [e.g. 1.0.0]

**Additional context**
Add any other context about the problem here.
```

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, include:

- **Clear title and description** of the enhancement
- **Step-by-step description** of the suggested enhancement
- **Explain why this enhancement would be useful**
- **List any alternatives** you've considered

### Your First Code Contribution

Unsure where to begin? You can start by looking through these issues:

- `good-first-issue` - Issues that should only require a few lines of code
- `help-wanted` - Issues that are a bit more involved

### Pull Requests

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 🛠️ Development Setup

### Prerequisites

- PHP 7.4+
- MySQL 5.7+
- Git
- Text editor (VS Code recommended)

### Setup Steps

1. **Clone your fork**
   ```bash
   git clone https://github.com/your-username/portfolio.git
   cd portfolio
   ```

2. **Set up database**
   ```bash
   mysql -u root -p < database/COMPLETE_DATABASE_SETUP.sql
   ```

3. **Configure environment**
   ```bash
   cp config/db.php.example config/db.php
   # Edit config/db.php with your credentials
   ```

4. **Start development server**
   ```bash
   php -S localhost:8000
   ```

5. **Open in browser**
   ```
   http://localhost:8000
   ```

---

## 📝 Coding Standards

### PHP

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Use meaningful variable and function names
- Add PHPDoc comments for functions and classes
- Use prepared statements for database queries

**Example:**
```php
<?php
/**
 * Get user by ID
 * 
 * @param int $userId User ID
 * @return array|null User data or null
 */
function getUserById($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
```

### JavaScript

- Use ES6+ features
- Use `const` and `let`, avoid `var`
- Use meaningful variable names
- Add JSDoc comments for functions

**Example:**
```javascript
/**
 * Toggle theme between light and dark mode
 * @param {string} theme - Theme name ('light' or 'dark')
 */
function toggleTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
}
```

### CSS

- Use BEM naming convention when possible
- Group related properties
- Use CSS variables for colors and spacing
- Mobile-first approach

**Example:**
```css
/* Component */
.card {
    background: var(--card-bg);
    border-radius: var(--radius);
    padding: var(--spacing-md);
}

/* Element */
.card__title {
    font-size: 1.5rem;
    font-weight: 700;
}

/* Modifier */
.card--featured {
    border: 2px solid var(--primary);
}
```

### HTML

- Use semantic HTML5 elements
- Include proper ARIA labels
- Maintain proper indentation
- Use meaningful class names

---

## 💬 Commit Guidelines

We follow [Conventional Commits](https://www.conventionalcommits.org/) specification.

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation only changes
- `style`: Changes that don't affect code meaning (formatting, etc.)
- `refactor`: Code change that neither fixes a bug nor adds a feature
- `perf`: Performance improvement
- `test`: Adding or updating tests
- `chore`: Changes to build process or auxiliary tools

### Examples

```bash
feat(projects): add project filtering functionality

Add ability to filter projects by category and technology.
Includes UI updates and backend API changes.

Closes #123
```

```bash
fix(contact): resolve email sending issue

Fixed SMTP configuration error that prevented emails from being sent.
Updated error handling to provide better user feedback.

Fixes #456
```

```bash
docs(readme): update installation instructions

Added more detailed steps for database setup and configuration.
```

---

## 🔄 Pull Request Process

### Before Submitting

1. ✅ Ensure your code follows the coding standards
2. ✅ Update documentation if needed
3. ✅ Test your changes thoroughly
4. ✅ Update the CHANGELOG.md if applicable
5. ✅ Ensure all tests pass
6. ✅ Rebase your branch on the latest main

### PR Template

```markdown
## Description
Brief description of what this PR does.

## Type of Change
- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update

## How Has This Been Tested?
Describe the tests you ran to verify your changes.

## Checklist
- [ ] My code follows the style guidelines of this project
- [ ] I have performed a self-review of my own code
- [ ] I have commented my code, particularly in hard-to-understand areas
- [ ] I have made corresponding changes to the documentation
- [ ] My changes generate no new warnings
- [ ] I have added tests that prove my fix is effective or that my feature works
- [ ] New and existing unit tests pass locally with my changes

## Screenshots (if applicable)
Add screenshots to help explain your changes.

## Related Issues
Closes #(issue number)
```

### Review Process

1. At least one maintainer must approve the PR
2. All CI checks must pass
3. No merge conflicts
4. Code review feedback must be addressed

### After Merge

- Delete your feature branch
- Update your local repository
- Celebrate! 🎉

---

## 🎨 Design Guidelines

### Colors

Use the existing color palette defined in CSS variables:
- Primary: `var(--p)`
- Accent: `var(--ac)`
- Text: `var(--tx)`

### Typography

- Headings: Inter font family
- Body: System font stack
- Code: Monospace

### Spacing

Use consistent spacing units:
- Small: 8px
- Medium: 16px
- Large: 24px
- XLarge: 32px

---

## 🧪 Testing

### Manual Testing

Before submitting a PR, test:
- ✅ All pages load correctly
- ✅ Forms submit properly
- ✅ Responsive design works on mobile/tablet/desktop
- ✅ Dark mode works correctly
- ✅ All links work
- ✅ No console errors

### Browser Testing

Test on:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

---

## 📞 Getting Help

- 💬 Join our [Discord](https://discord.gg/yourserver)
- 📧 Email: eliasaraya142@gmail.com
- 🐛 [Open an issue](https://github.com/yourusername/yourrepo/issues)

---

## 🙏 Recognition

Contributors will be recognized in:
- README.md contributors section
- CHANGELOG.md for their contributions
- GitHub contributors page

---

Thank you for contributing! 🚀
