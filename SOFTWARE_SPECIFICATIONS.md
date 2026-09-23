# KCC E-Voting System (KEVS) - Software Specifications

## Complete List of Software, Tools & Technologies Used for Development and Implementation

### Backend Framework & Server
- **CodeIgniter 4** (v4.0+) - PHP web framework
- **PHP** (v8.1+) - Server-side programming language
  - Required extensions:
    - `intl` - Internationalization support
    - `mbstring` - Multi-byte string functions
    - `json` - JSON encoding/decoding
    - `mysqlnd` - MySQL Native Driver
    - `libcurl` - cURL library for HTTP requests

### Database & Data Management
- **MySQL** (via XAMPP) - Relational database management system
- **MariaDB** (included in XAMPP) - MySQL-compatible database
- **phpMyAdmin** - Database administration interface (included in XAMPP)

### Server Environment
- **XAMPP** - Apache, MySQL, PHP bundle
  - **Apache HTTP Server** - Web server
  - **Apache modules** - For URL rewriting and routing

### Package Management & Dependencies
- **Composer** (PHP dependency manager)
  - **codeigniter4/framework** (^4.0) - Core CI4 framework
  - **fakerphp/faker** (^1.9) - Fake data generation for testing
  - **mikey179/vfsstream** (^1.6) - Virtual file system for testing
  - **phpunit/phpunit** (^10.5.16) - Unit testing framework

### Testing & Quality Assurance
- **PHPUnit** (v10.5.16+) - PHP unit testing framework
- **Code Coverage Tools** - PHPUnit built-in coverage reporting

### Frontend Technologies
- **HTML5** - Markup language
- **CSS3** - Styling
  - Custom CSS files:
    - burger-menu.css
    - dashboard.css
    - modern-ui.css
    - registration-modern.css
    - simple.css
    - student-responsive.css
    - students-filter.css
    - style.css
    - theme-unified.css

- **JavaScript (Vanilla)** - Client-side scripting
  - Custom JavaScript files:
    - burger-menu.js
    - dashboard.js
    - main.js
    - registration-modern.js
    - theme.js
    - validation.js

### Face Recognition & Biometric Features
- **Azure Face API** - Face detection and verification service
  - Integrated capabilities:
    - Face detection
    - Face verification
    - Face attribute analysis

### External CDN Resources
- **Google Fonts** - Poppins font family (weights: 300, 400, 500, 600, 700)
- **Bootstrap Icons** - Icon library (v1.10.0+)
  - CDN: `https://cdn.jsdelivr.net/npm/bootstrap-icons`

### Development Tools & IDEs
- **Visual Studio Code** - Source code editor
- **Git** - Version control system
- **PHP CLI** - Command-line interface for PHP

### Build & Automation Tools
- **Spark CLI** - CodeIgniter command-line tool
  - Located: `./spark` file in root directory

### Configuration & Environment Files
- `.env` - Environment configuration file
- `composer.json` - PHP dependencies configuration
- `phpunit.xml.dist` - PHPUnit configuration
- `env` - Environment template file

### Database Features Used
- **UTF-8mb4 Character Set** - Unicode support
- **InnoDB Engine** - Transactions support
- **Foreign Keys** - Referential integrity
- **Indexes** - Performance optimization
- **Timestamps** - Automatic created_at tracking

### Version Control & Documentation
- **Markdown** - Documentation format (.md files)
- **LICENSE** - MIT License

---

## System Requirements Summary

### Minimum Server Requirements
- PHP 8.1 or higher
- MySQL 5.7 or higher (or MariaDB 10.2+)
- Apache 2.4+
- 512MB RAM minimum
- 250MB disk space

### Browser Requirements
- Modern browser with:
  - JavaScript enabled
  - WebGL support (for face recognition features)
  - HTML5 Canvas support
  - CSS3 support

### Optional/Recommended
- Node.js (if extending with npm packages)
- Composer 2.0+
- PHPMyAdmin for database management

---

## Development Dependencies
```
PHP 8.1+
├── CodeIgniter 4.0+
├── Composer
└── Dependencies:
    ├── fakerphp/faker (testing)
    ├── mikey179/vfsstream (testing)
    └── phpunit/phpunit (testing)
```

## Frontend Dependencies
```
HTML5 + CSS3 + JavaScript (Vanilla)
├── face-api.min.js (biometric features)
├── Bootstrap Icons (icons via CDN)
├── Google Fonts (typography)
└── Custom CSS & JavaScript files
```

---

## Key Technology Stack Summary
| Layer | Technology | Version |
|-------|-----------|---------|
| **Backend** | CodeIgniter 4 | 4.0+ |
| **Language** | PHP | 8.1+ |
| **Database** | MySQL/MariaDB | 5.7+/10.2+ |
| **Frontend** | HTML5/CSS3/JavaScript | Latest |
| **Biometrics** | face-api.js | Latest |
| **Testing** | PHPUnit | 10.5.16+ |
| **Package Manager** | Composer | 2.0+ |
| **Server** | Apache | 2.4+ |
| **IDE** | Visual Studio Code | Latest |

---

## Build & Deployment Commands
```bash
# Install dependencies
composer install

# Run migrations
php spark migrate

# Run tests
php spark test
# OR
phpunit

# Clear cache
php spark cache:clear
```

---

Generated: June 1, 2026
Project: KCC E-Voting System (KEVS)
