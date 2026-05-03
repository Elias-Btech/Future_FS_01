#!/bin/bash

# ============================================
# Professional Portfolio - Setup Script
# ============================================
# This script automates the initial setup
# of the portfolio website
# ============================================

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_header() {
    echo -e "${BLUE}============================================${NC}"
    echo -e "${BLUE}  Professional Portfolio - Setup${NC}"
    echo -e "${BLUE}============================================${NC}"
    echo ""
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

# Check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Main setup
print_header

# Step 1: Check prerequisites
print_info "Checking prerequisites..."

if command_exists php; then
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
    print_success "PHP $PHP_VERSION installed"
else
    print_error "PHP is not installed. Please install PHP 7.4 or higher."
    exit 1
fi

if command_exists mysql; then
    print_success "MySQL installed"
else
    print_warning "MySQL not found. You'll need to install it manually."
fi

if command_exists git; then
    print_success "Git installed"
else
    print_warning "Git not found. Version control features will be limited."
fi

echo ""

# Step 2: Create necessary directories
print_info "Creating directory structure..."

mkdir -p logs
mkdir -p assets/uploads
mkdir -p database/backups
mkdir -p documentation

print_success "Directories created"
echo ""

# Step 3: Set permissions
print_info "Setting file permissions..."

chmod 755 logs/
chmod 755 assets/uploads/
chmod 644 config/*.php 2>/dev/null || true

print_success "Permissions set"
echo ""

# Step 4: Create .gitkeep files
print_info "Creating .gitkeep files..."

touch logs/.gitkeep
touch assets/uploads/.gitkeep
touch database/backups/.gitkeep

print_success ".gitkeep files created"
echo ""

# Step 5: Copy configuration templates
print_info "Setting up configuration files..."

if [ ! -f config/db.php ]; then
    cat > config/db.php << 'EOF'
<?php
/**
 * Database Configuration
 * 
 * IMPORTANT: Update these values with your actual database credentials
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// Create PDO connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check your configuration.");
}
EOF
    print_success "Database configuration template created"
    print_warning "Please edit config/db.php with your database credentials"
else
    print_info "Database configuration already exists"
fi

echo ""

# Step 6: Database setup
print_info "Database setup..."
echo ""
echo "Would you like to set up the database now? (y/n)"
read -r setup_db

if [ "$setup_db" = "y" ] || [ "$setup_db" = "Y" ]; then
    echo "Enter MySQL username:"
    read -r db_user
    echo "Enter MySQL password:"
    read -rs db_pass
    echo "Enter database name:"
    read -r db_name
    
    echo ""
    print_info "Creating database and importing schema..."
    
    mysql -u "$db_user" -p"$db_pass" -e "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -u "$db_user" -p"$db_pass" "$db_name" < database/COMPLETE_DATABASE_SETUP.sql
    
    print_success "Database setup complete"
else
    print_warning "Skipping database setup. You can run it manually later."
fi

echo ""

# Step 7: Create .htaccess for Apache
print_info "Creating .htaccess file..."

cat > .htaccess << 'EOF'
# Professional Portfolio - Apache Configuration

# Enable Rewrite Engine
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Force HTTPS (uncomment in production)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Remove www (or add www, choose one)
    # RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
    # RewriteRule ^(.*)$ https://%1/$1 [R=301,L]
    
    # Route all requests to index.php
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Disable directory browsing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protect configuration files
<FilesMatch "\.(env|ini|log|sh|sql)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
</IfModule>
EOF

print_success ".htaccess created"
echo ""

# Step 8: Final checks
print_info "Running final checks..."

if [ -f "index.html" ]; then
    print_success "index.html found"
else
    print_error "index.html not found!"
fi

if [ -f "manifest.json" ]; then
    print_success "manifest.json found"
else
    print_warning "manifest.json not found"
fi

if [ -f "sw.js" ]; then
    print_success "Service worker found"
else
    print_warning "Service worker not found"
fi

echo ""

# Step 9: Summary
print_header
echo -e "${GREEN}Setup Complete!${NC}"
echo ""
echo "Next steps:"
echo "1. Edit config/db.php with your database credentials"
echo "2. Edit config/email.php for email functionality"
echo "3. Update personal information in index.html"
echo "4. Replace assets/elias-nobg.png with your photo"
echo "5. Add your certificates to assets/certificates/"
echo "6. Test the website: php -S localhost:8000"
echo ""
echo "Documentation: ./documentation/"
echo "Troubleshooting: ./documentation/TROUBLESHOOTING.md"
echo ""
print_success "Happy coding! 🚀"
echo ""
