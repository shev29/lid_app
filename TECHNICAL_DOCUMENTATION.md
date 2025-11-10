# Dokumentasi Teknis: Logisteed LID Application

**Versi:** 1.0  
**Terakhir Diperbarui:** 2025-11-10  
**Penulis:** Tim Pengembangan Logisteed  

---

## Daftar Isi

1. [Ringkasan Proyek](#1-ringkasan-proyek)
2. [Arsitektur Sistem](#2-arsitektur-sistem)
3. [Teknologi Stack](#3-teknologi-stack)
4. [Struktur Direktori](#4-struktur-direktori)
5. [Modul Aplikasi](#5-modul-aplikasi)
6. [Instalasi dan Konfigurasi](#6-instalasi-dan-konfigurasi)
7. [Struktur Database](#7-struktur-database)
8. [Dokumentasi API](#8-dokumentasi-api)
9. [Fitur Keamanan](#9-fitur-keamanan)
10. [Panduan Pengembangan](#10-panduan-pengembangan)
11. [Panduan Deployment](#11-panduan-deployment)
12. [Troubleshooting](#12-troubleshooting)

---

## 1. Ringkasan Proyek

### 1.1 Tentang Aplikasi

Logisteed LID Application adalah sistem enterprise berbasis web yang dirancang untuk mengelola proses bisnis logistik dan procurement di Logisteed Indonesia. Aplikasi ini mengotomatisasi alur persetujuan dokumen, pembelian, dan manajemen aplikasi internal.

### 1.2 Tujuan Utama

- Otomatisasi proses persetujuan dokumen (Document Approval System)
- Manajemen procurement dan purchasing yang terintegrasi
- Integrasi dengan sistem CEISA H2H (Head-to-Head) untuk kepabeanan
- Manajemen aplikasi dan storage internal
- Pelaporan dan monitoring real-time

### 1.3 Karakteristik Aplikasi

- **Jenis Aplikasi**: Enterprise Web Application
- **Arsitektur**: Modular Monolith dengan Laravel Modules
- **Database**: MySQL/MariaDB
- **Frontend**: Blade Templates + Alpine.js + Bootstrap 5
- **Backend**: PHP 8.2 + Laravel 11.9

---

## 2. Arsitektur Sistem

### 2.1 Arsitektur Aplikasi

Aplikasi menggunakan arsitektur **Modular Monolith** dengan pola MVC (Model-View-Controller) yang diperluas menggunakan Laravel Modules.

```
┌─────────────────────────────────────────────────────────────┐
│                    Presentation Layer                        │
│              (Blade Templates + Alpine.js)                   │
└──────────────────┬──────────────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────────────┐
│                   Application Layer                          │
│                   (Controllers + Jobs)                       │
└──────────────────┬──────────────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────────────┐
│                    Business Layer                            │
│              (Services + Rule Engine)                        │
└──────────────────┬──────────────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────────────┐
│                      Data Layer                              │
│                  (Models + Repository)                       │
└──────────────────┬──────────────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────────────┐
│                      Database Layer                          │
│                     (MySQL/MariaDB)                          │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Pola Desain Utama

1. **Repository Pattern**: Abstraksi akses database melalui Models
2. **Service Layer Pattern**: Logic bisnis dipisahkan dari Controllers
3. **Job Queue Pattern**: Proses asinkron untuk email dan PDF generation
4. **Middleware Pattern**: Filter request dan response
5. **Module Pattern**: Pemisahan fitur berdasarkan domain bisnis

### 2.3 Alur Request-Response

```
User Request → Middleware Chain → Router → Controller
     ↓
Controller → Service/Model → Database
     ↓
Service → Job Queue (for async tasks)
     ↓
Controller → View/JSON Response → User
```

---

## 3. Teknologi Stack

### 3.1 Backend Technologies

#### Core Framework
- **PHP**: 8.2+ (Required)
- **Laravel Framework**: 11.9
- **Laravel Modules (nwidart)**: 11.0 - Untuk arsitektur modular

#### Database & ORM
- **MySQL/MariaDB**: Database utama
- **Eloquent ORM**: Object-Relational Mapping
- **Laravel Migrations**: Database version control

#### PDF & Document Processing
- **DomPDF**: 3.0 - PDF generation dari HTML
- **mPDF**: 8.2 - Advanced PDF processing
- **FPDF/FPDI**: PDF manipulation dan merging
- **TCPDF**: 6.8 - PDF library dengan fitur advanced
- **PhpSpreadsheet**: 1.29 - Excel file processing
- **PhpWord**: 1.3 - Word document processing
- **LibMergePDF**: 3.1 - Merge multiple PDFs

#### Image Processing
- **Intervention Image**: 3.8 + Laravel Integration 1.3
- **Simple QRCode**: 4.2 - QR code generation

#### Email & Communication
- **Symfony Mailer**: 7.1
- **PHP MIME Mail Parser**: 9.0 - Email parsing
- **MAPI**: 1.4 - Microsoft MAPI integration

#### Data Processing
- **Maatwebsite Excel**: 3.1 - Excel import/export
- **Yajra DataTables**: 11.1 - Server-side DataTables

#### API & Integration
- **Google API Client**: 2.17 - Google services integration
- **Guzzle HTTP**: 2.7 - HTTP client untuk API calls

#### Security & Data Handling
- **Mews Purifier**: 3.4 - HTML sanitization
- **Spatie Laravel Backup**: 9.3 - Automated backups

### 3.2 Frontend Technologies

#### UI Framework
- **Bootstrap**: 5.x - CSS framework
- **Alpine.js**: Lightweight JavaScript framework
- **jQuery**: 3.7.x - DOM manipulation
- **Popper.js**: Tooltip & popover positioning

#### Data Tables
- **DataTables.js**: 2.x - Interactive tables
- **DataTables Bootstrap 5**: Bootstrap integration
- **DataTables Responsive**: Mobile-responsive tables

#### UI Components
- **Select2**: Advanced select boxes
- **Virtual Select**: Virtual scrolling select
- **SweetAlert2**: Beautiful alert dialogs
- **Magnific Popup**: Lightbox plugin
- **Snackbar**: Toast notifications

#### Build Tools
- **Vite**: 5.3.4 - Modern build tool
- **Laravel Vite Plugin**: 1.0.5
- **Axios**: 1.6.4 - HTTP client

### 3.3 Development Tools

- **Composer**: PHP dependency management
- **NPM**: JavaScript package management
- **Laravel Tinker**: 2.9 - REPL untuk Laravel
- **Laravel Pint**: 1.13 - Code style fixer
- **PHPUnit**: 11.0 - Testing framework
- **Laravel Sail**: 1.26 - Docker development environment
- **Faker**: 1.23 - Test data generation
- **Mockery**: 1.6 - Mocking framework

### 3.4 Server Requirements

#### Minimum Requirements
- **PHP**: >= 8.2
- **MySQL**: >= 5.7 atau MariaDB >= 10.3
- **Composer**: >= 2.0
- **Node.js**: >= 18.x
- **NPM**: >= 9.x

#### PHP Extensions Required
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- GD
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- Tokenizer
- XML
- Zip

---

## 4. Struktur Direktori

### 4.1 Root Directory Structure

```
lid_app/
├── app/                      # Core application code
│   ├── Http/                 # HTTP layer (Controllers, Middleware)
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic services
│   ├── Jobs/                 # Queue jobs
│   ├── Providers/            # Service providers
│   └── Helpers/              # Helper functions
├── bootstrap/                # Application bootstrapping
├── config/                   # Configuration files
├── database/                 # Database files
│   ├── migrations/           # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── Modules/                  # Modular features
│   ├── ApplicationManager/   # Application & storage management
│   ├── CeisaH2h/            # CEISA integration
│   ├── DocumentApproval/     # Document approval system
│   ├── Home/                 # Homepage & dashboard
│   └── ProcurementPurchasing/# Procurement & purchasing
├── public/                   # Public web root
│   ├── css/                  # Compiled CSS
│   ├── js/                   # Compiled JS
│   └── index.php             # Entry point
├── resources/                # Raw assets
│   ├── css/                  # Source CSS
│   ├── js/                   # Source JS
│   ├── views/                # Blade templates
│   └── lang/                 # Language files
├── routes/                   # Route definitions
│   ├── web.php               # Web routes
│   ├── api.php               # API routes
│   └── console.php           # Console routes
├── storage/                  # File storage
│   ├── app/                  # Application files
│   ├── framework/            # Framework files
│   └── logs/                 # Log files
├── tests/                    # Test files
├── vendor/                   # Composer dependencies
├── composer.json             # PHP dependencies
├── package.json              # JS dependencies
├── vite.config.js            # Vite configuration
├── phpunit.xml               # PHPUnit configuration
└── artisan                   # CLI tool
```

### 4.2 Module Structure

Setiap modul mengikuti struktur standar:

```
Modules/[ModuleName]/
├── app/
│   ├── Http/
│   │   └── Controllers/      # Module controllers
│   ├── Models/               # Module models
│   └── Providers/            # Module service providers
├── config/                   # Module configuration
├── database/
│   ├── migrations/           # Module migrations
│   └── seeders/              # Module seeders
├── resources/
│   ├── assets/               # Module assets
│   └── views/                # Module views
├── routes/
│   ├── web.php               # Module web routes
│   └── api.php               # Module API routes
├── tests/                    # Module tests
├── module.json               # Module metadata
└── composer.json             # Module dependencies
```

---

## 5. Modul Aplikasi

### 5.1 Home Module

**Deskripsi**: Modul utama untuk dashboard dan halaman beranda aplikasi.

**Lokasi**: `Modules/Home/`

**Fitur Utama**:
- Dashboard overview
- Notifikasi dan alerts
- Quick access menu
- User activity summary

**Controllers**:
- `HomeController.php` - Menangani rendering halaman utama

**Route Prefix**: `/`

### 5.2 DocumentApproval Module

**Deskripsi**: Modul untuk sistem persetujuan dokumen dengan alur workflow yang dapat dikonfigurasi.

**Lokasi**: `Modules/DocumentApproval/`

**Fitur Utama**:
- Multi-stage approval workflow
- Document type management
- Digital signature flow
- Email notifications
- Document history tracking
- PDF generation untuk berbagai jenis dokumen

**Controllers**:
- `DocumentApprovalController.php` - Main controller (1.6MB, ~50k lines)
- `ApiExternalController.php` - API untuk integrasi eksternal
- `MigrationBmlLogController.php` - Data migration dari sistem lama

**Models**:
- `DocumentApprovalModel.php` - Eloquent model untuk approval documents
- `MigrationBmlLogModel.php` - Model untuk migration data

**Key Features**:

#### Document Types
1. **Order Form** (Type 1)
   - Template: F.PUR-02.01_ORDER_FORM_FINAL.xlsx
   - Support hingga 25 item rows
   - Multi-signer support

2. **Application & PO Form** (Type 2)
   - Templates: Application + PO
   - Combined PDF output
   - Company header berbasis application type

3. **Comparison Form** (Type 6)
   - Template: TEMPLATE_COMPARISON_FORM.xlsx
   - Landscape orientation
   - Vendor comparison data

4. **Inspection Form** (Type 4)
   - Template: F.PUR-02.02-04_INSPECTION_FINAL.xlsx
   - Delivery & inspection details

#### Approval Flow
```
Document Creation → Validation → Submit
     ↓
First Approver → Review → Action (Approve/Reject/Send Back)
     ↓
Second Approver → Review → Action
     ↓
... (Multi-stage)
     ↓
Final Approval → Status: COMPLETED → Notification
```

#### PDF Generation Process
1. Load Excel template dari storage
2. Populate data ke cells tertentu
3. Convert to PDF menggunakan mPDF
4. Add QR code untuk tracking
5. Combine multiple PDFs jika perlu
6. Store final PDF
7. Send email notification

**Route Prefix**: `/doc_approval`

### 5.3 ProcurementPurchasing Module

**Deskripsi**: Modul untuk mengelola proses procurement dan purchasing.

**Lokasi**: `Modules/ProcurementPurchasing/`

**Fitur Utama**:
- Purchase requisition
- Purchase order management
- Vendor management
- Inspection forms
- Freight price requests
- Excel import/export
- Integration dengan DocumentApproval

**Controllers**:
- `ProcurementPurchasingController.php` - Main controller (775KB)
- `FreightPriceRequestController.php` - Freight pricing

**Models**:
- `ProcurementPurchasingModel.php` - Main model
- `FreightPriceRequestModel.php` - Freight pricing model

**Exports**:
- Excel export untuk laporan procurement

**Key Features**:
- Multi-currency support
- Payment terms management
- Delivery tracking
- Budget control
- Integration dengan approval flow

**Route Prefix**: `/proc_pur`

### 5.4 ApplicationManager Module

**Deskripsi**: Modul untuk mengelola aplikasi internal dan file storage.

**Lokasi**: `Modules/ApplicationManager/`

**Fitur Utama**:
- Application listing dan management
- Storage management
- File upload/download
- Access control
- Usage monitoring

**Controllers**:
- `ApplicationManagerController.php` - Application CRUD
- `StorageManagerController.php` - Storage operations (25KB)

**Models**:
- `ApplicationManagerModel.php` - Application data
- `StorageManagerModel.php` - Storage data

**Key Features**:
- File versioning
- Storage quota management
- Access logs
- Automatic cleanup old files

**Route Prefix**: `/app_manager`

### 5.5 CeisaH2h Module

**Deskripsi**: Modul integrasi dengan sistem CEISA (Customs Information System and Automation) untuk keperluan kepabeanan.

**Lokasi**: `Modules/CeisaH2h/`

**Fitur Utama**:
- H2H (Head-to-Head) integration dengan CEISA
- Data submission ke Bea Cukai
- Response handling dan logging
- Error tracking dan retry mechanism

**Controllers**:
- `CeisaH2hController.php` - Main controller (17KB)

**Models**:
- `CeisaH2hModel.php` - CEISA data model

**Key Features**:
- Automated data submission
- Response parsing
- Error handling
- Audit trail
- Retry mechanism untuk failed submissions

**Route Prefix**: `/ceisa_h2h`

---

## 6. Instalasi dan Konfigurasi

### 6.1 Persyaratan Sistem

Sebelum instalasi, pastikan server memenuhi persyaratan:

```bash
# Check PHP version
php -v  # Must be >= 8.2

# Check required PHP extensions
php -m | grep -E "bcmath|ctype|curl|dom|fileinfo|gd|json|mbstring|openssl|pdo|tokenizer|xml|zip"

# Check MySQL version
mysql --version  # Must be >= 5.7

# Check Composer
composer --version  # Must be >= 2.0

# Check Node.js
node --version  # Must be >= 18.x
```

### 6.2 Langkah Instalasi

#### Step 1: Clone Repository

```bash
git clone https://github.com/shev29/lid_app.git
cd lid_app
```

#### Step 2: Install PHP Dependencies

```bash
composer install
```

#### Step 3: Install JavaScript Dependencies

```bash
npm install
```

#### Step 4: Environment Configuration

```bash
# Copy environment file
cp .env.example .env  # Jika tersedia, atau buat manual

# Generate application key
php artisan key:generate
```

#### Step 5: Configure .env File

Edit file `.env` dan sesuaikan konfigurasi:

```env
APP_NAME="Logisteed LID App"
APP_ENV=production
APP_KEY=  # Will be generated by key:generate
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://app.logisteed.id

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lid_app
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_CONNECTION=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@logisteed.id
MAIL_FROM_NAME="${APP_NAME}"

# Google API (Optional)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

# Backup Configuration
BACKUP_DISK=backups
```

#### Step 6: Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE lid_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Run migrations
php artisan migrate

# (Optional) Seed database
php artisan db:seed
```

#### Step 7: Storage Linking

```bash
# Create symbolic link for storage
php artisan storage:link

# Create required directories
mkdir -p storage/app/private/template
mkdir -p storage/app/private/doc_approval
mkdir -p storage/app/private/fonts
mkdir -p storage/app/temp
```

#### Step 8: Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

#### Step 9: Set Permissions

```bash
# Set correct permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Step 10: Configure Web Server

**For Nginx:**

```nginx
server {
    listen 80;
    server_name app.logisteed.id;
    root /var/www/lid_app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**For Apache:**

```apache
<VirtualHost *:80>
    ServerName app.logisteed.id
    DocumentRoot /var/www/lid_app/public

    <Directory /var/www/lid_app/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/lid_app_error.log
    CustomLog ${APACHE_LOG_DIR}/lid_app_access.log combined
</VirtualHost>
```

#### Step 11: Queue Worker Setup

Untuk background jobs (email, PDF generation):

```bash
# Using Supervisor
sudo nano /etc/supervisor/conf.d/lid-worker.conf
```

```ini
[program:lid-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lid_app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/lid_app/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start lid-worker:*
```

#### Step 12: Cron Jobs

```bash
# Edit crontab
crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/lid_app && php artisan schedule:run >> /dev/null 2>&1
```

### 6.3 Post-Installation

```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify installation
php artisan about
```

### 6.4 Troubleshooting Installation

**Problem**: "Class 'ZipArchive' not found"
```bash
sudo apt-get install php8.2-zip
sudo systemctl restart php8.2-fpm
```

**Problem**: PDF generation fails
```bash
# Install required fonts
sudo apt-get install fontconfig libxrender1
# Copy fonts to storage/app/fonts/
```

**Problem**: Queue jobs not processing
```bash
# Check queue worker status
sudo supervisorctl status lid-worker:*
# Restart workers
sudo supervisorctl restart lid-worker:*
```

---

## 7. Struktur Database

### 7.1 Schema Overview

Database menggunakan skema relasional dengan tabel-tabel utama untuk:
- User management dan authentication
- Document approval workflow
- Procurement dan purchasing data
- Master data (companies, employees, departments)
- Email accounts dan notifications
- Audit logs

### 7.2 Tabel-Tabel Utama

#### Core Tables

**users** - User authentication
```sql
- id (PK)
- name
- email (unique)
- email_verified_at
- password
- remember_token
- created_at
- updated_at
```

**master_employees** - Employee master data
```sql
- employee_id (PK)
- employee_name
- employee_email
- position_id
- department_id
- company_id
- is_active
- created_at
- updated_at
```

**master_companies** - Company master data
```sql
- company_id (PK)
- company_name
- company_code
- address
- phone
- is_active
- created_at
- updated_at
```

**master_departments** - Department master data
```sql
- department_id (PK)
- department_name
- company_id (FK)
- is_active
- created_at
- updated_at
```

#### Document Approval Tables

**doc_approval_header** - Document approval main table
```sql
- doc_approval_id (PK)
- document_id
- doc_type_id (FK)
- doc_number
- company_id (FK)
- department_id (FK)
- applicant_id (FK)
- transaction_status_id
- approval_status_id
- created_at
- updated_at
- deleted_at
```

**doc_approval_flow_sign** - Approval flow signatures
```sql
- sign_flow_id (PK)
- doc_approval_id (FK)
- employee_id (FK)
- flow_as (APPROVER, REVIEWER, etc.)
- flow_order
- sign_status
- sign_date
- comments
- created_at
- updated_at
```

**master_doc_types** - Document types
```sql
- doc_type_id (PK)
- doc_type_name
- doc_group_id (FK)
- doc_order
- is_active
- is_view
- company_id (FK)
- created_at
- updated_at
```

**master_doc_groups** - Document groups
```sql
- doc_group_id (PK)
- group_name
- group_description
- is_active
- created_at
- updated_at
```

#### Procurement Tables

**proc_purchase_order** - Purchase orders
```sql
- po_id (PK)
- po_number
- doc_approval_id (FK)
- vendor_id (FK)
- company_id (FK)
- po_date
- delivery_date
- payment_terms
- currency
- total_amount
- status
- created_at
- updated_at
```

**proc_purchase_order_items** - PO items
```sql
- po_item_id (PK)
- po_id (FK)
- item_code
- item_name
- quantity
- unit_price
- unit
- total_price
- created_at
- updated_at
```

**master_vendors** - Vendor master data
```sql
- vendor_id (PK)
- vendor_name
- vendor_code
- address
- phone
- email
- bank_account
- is_active
- created_at
- updated_at
```

#### Email & Notifications

**master_email_accounts** - Email account configuration
```sql
- id (PK)
- account_name
- email_address
- smtp_host
- smtp_port
- smtp_username
- smtp_password
- encryption_type
- is_active
- created_at
- updated_at
```

**notification_logs** - Notification history
```sql
- log_id (PK)
- doc_approval_id (FK)
- employee_id (FK)
- notification_type
- sent_at
- status
- error_message
- created_at
- updated_at
```

#### Jobs & Queue

**jobs** - Queue jobs
```sql
- id (PK)
- queue
- payload (text)
- attempts
- reserved_at
- available_at
- created_at
```

**failed_jobs** - Failed queue jobs
```sql
- id (PK)
- uuid (unique)
- connection
- queue
- payload (longtext)
- exception (longtext)
- failed_at
```

### 7.3 Relationships

```
master_companies
    └── master_departments
            └── master_employees
                    ├── users (1:1)
                    └── doc_approval_header (1:N as applicant)

doc_approval_header
    ├── master_doc_types (N:1)
    ├── doc_approval_flow_sign (1:N)
    ├── proc_purchase_order (1:1)
    └── notification_logs (1:N)

proc_purchase_order
    ├── master_vendors (N:1)
    └── proc_purchase_order_items (1:N)
```

### 7.4 Indexes

Key indexes untuk performa:

```sql
-- Document Approval
CREATE INDEX idx_doc_approval_status ON doc_approval_header(transaction_status_id, approval_status_id);
CREATE INDEX idx_doc_approval_company ON doc_approval_header(company_id);
CREATE INDEX idx_doc_approval_applicant ON doc_approval_header(applicant_id);

-- Flow Sign
CREATE INDEX idx_flow_sign_doc ON doc_approval_flow_sign(doc_approval_id);
CREATE INDEX idx_flow_sign_employee ON doc_approval_flow_sign(employee_id);

-- Purchase Order
CREATE INDEX idx_po_company ON proc_purchase_order(company_id);
CREATE INDEX idx_po_status ON proc_purchase_order(status);
```

---

## 8. Dokumentasi API

### 8.1 Authentication

Aplikasi menggunakan session-based authentication dengan token validation.

#### Login
```http
POST /auth_login
Content-Type: application/x-www-form-urlencoded

username=user@example.com
password=secretpassword
```

Response:
```json
{
    "success": true,
    "message": "Login successful",
    "redirect": "/"
}
```

#### Logout
```http
GET /logout
```

### 8.2 Document Approval API

#### Get Document Types
```http
GET /doc_approval/getDocumentType?companyId={id}&viewType={type}
```

Response:
```json
[
    {
        "doc_type_id": 1,
        "doc_type_name": "Order Form",
        "group_name": "Procurement",
        "is_active": 1
    }
]
```

#### Submit Document
```http
POST /doc_approval/saveForm
Content-Type: multipart/form-data

documentType: 1
departmentOrder: DEPT-001
approver[]: employee_id_1
approver[]: employee_id_2
attachmentFileDocument[]: file1.pdf
actionType: SUBMIT
```

Response:
```json
{
    "success": true,
    "message": "Document submitted successfully",
    "doc_approval_id": 12345
}
```

#### Get Approval List
```http
GET /doc_approval/getApprovalList?companyId={id}&status={status}&page={page}
```

Response:
```json
{
    "data": [
        {
            "doc_approval_id": 12345,
            "doc_number": "DOC-2024-001",
            "doc_type_name": "Order Form",
            "applicant_name": "John Doe",
            "status": "PENDING",
            "created_at": "2024-11-01 10:00:00"
        }
    ],
    "pagination": {
        "total": 100,
        "per_page": 15,
        "current_page": 1,
        "last_page": 7
    }
}
```

#### Approve/Reject Document
```http
POST /doc_approval/updateAction
Content-Type: application/json

{
    "docApprovalId": 12345,
    "actionType": "APPROVE",
    "commentAction": "Approved as requested"
}
```

### 8.3 Procurement API

#### Get PO List
```http
GET /proc_pur/getPoList?companyId={id}&status={status}
```

#### Create PO
```http
POST /proc_pur/createPo
Content-Type: application/json

{
    "doc_approval_id": 12345,
    "vendor_id": 1,
    "items": [
        {
            "item_code": "ITM-001",
            "quantity": 10,
            "unit_price": 50000
        }
    ]
}
```

### 8.4 External API (untuk integrasi)

#### Submit Document (External)
```http
POST /api/external/submitDocument
Authorization: Bearer {api_key}
Content-Type: application/json

{
    "company_code": "COMP01",
    "doc_type": "order_form",
    "data": {...}
}
```

---

## 9. Fitur Keamanan

### 9.1 Authentication & Authorization

#### Session Management
- Session timeout: 120 menit (configurable)
- Token-based validation
- Automatic logout on inactivity
- Prevent back button after logout

#### Password Security
- Minimum length: 8 characters
- Password hashing: BCrypt
- Password reset with email verification
- Force password change on first login

### 9.2 Input Validation & Sanitization

#### Request Middleware
- `EscapeRequestInput`: Escape HTML entities
- `DecodeModSecurityPlaceholders`: Handle ModSecurity placeholders
- CSRF protection on all forms
- XSS prevention

#### File Upload Security
- File type validation
- File size limits
- Virus scanning (recommended)
- Secure file naming
- Private storage location

### 9.3 SQL Injection Prevention

- Prepared statements via Eloquent ORM
- Query parameter binding
- Input validation and sanitization

### 9.4 CSRF Protection

All POST/PUT/DELETE requests require CSRF token:
```php
@csrf // In Blade templates
```

### 9.5 API Security

- API key authentication
- Rate limiting (configurable)
- IP whitelisting
- Request logging

### 9.6 Security Headers

```php
// Added in middleware
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000
```

### 9.7 Data Encryption

- Sensitive data encrypted at rest
- HTTPS/TLS for data in transit
- Database connection encryption
- Encrypted email passwords in configuration

### 9.8 Audit Logging

- User actions logged
- Document access tracked
- Failed login attempts logged
- Approval flow audit trail

### 9.9 Backup & Recovery

- Automated daily backups (Spatie Laravel Backup)
- Backup encryption
- Off-site backup storage
- Disaster recovery plan

### 9.10 Security Best Practices

1. **Keep Dependencies Updated**
   ```bash
   composer update
   npm update
   ```

2. **Regular Security Audits**
   ```bash
   composer audit
   npm audit
   ```

3. **Environment Security**
   - Never commit `.env` file
   - Use strong database passwords
   - Rotate API keys regularly
   - Limit database user privileges

4. **Server Hardening**
   - Disable directory listing
   - Hide server version
   - Configure firewall
   - Regular security patches

---

## 10. Panduan Pengembangan

### 10.1 Coding Standards

#### PHP Coding Style
Aplikasi menggunakan PSR-12 coding standard. Gunakan Laravel Pint untuk formatting:

```bash
# Format semua files
./vendor/bin/pint

# Format specific file
./vendor/bin/pint app/Http/Controllers/MyController.php
```

#### Naming Conventions

**Classes**
```php
// PascalCase
class DocumentApprovalController extends Controller
class UserService
```

**Methods**
```php
// camelCase
public function getDocumentList()
public function saveForm(Request $request)
```

**Variables**
```php
// camelCase
$documentId = 123;
$approvalList = [];
```

**Database Tables**
```sql
-- snake_case, plural
doc_approval_header
master_employees
```

**Database Columns**
```sql
-- snake_case
employee_id
created_at
```

### 10.2 Development Workflow

#### Branch Strategy

```
main (production)
  ├── develop (staging)
       ├── feature/document-approval-v2
       ├── feature/new-report
       └── hotfix/critical-bug-fix
```

#### Workflow Steps

1. **Create Feature Branch**
```bash
git checkout develop
git pull origin develop
git checkout -b feature/your-feature-name
```

2. **Development**
```bash
# Make changes
# Test locally
php artisan test
npm run build
```

3. **Commit Changes**
```bash
git add .
git commit -m "feat: add new document approval feature"
```

Commit message format:
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation
- `style:` Code style changes
- `refactor:` Code refactoring
- `test:` Adding tests
- `chore:` Maintenance tasks

4. **Push and Create PR**
```bash
git push origin feature/your-feature-name
# Create Pull Request on GitHub
```

5. **Code Review**
- At least 1 approval required
- All tests must pass
- No merge conflicts

6. **Merge to Develop**
```bash
git checkout develop
git merge feature/your-feature-name
git push origin develop
```

### 10.3 Creating New Module

```bash
# Generate new module
php artisan module:make ModuleName

# Enable module
php artisan module:enable ModuleName

# Create controller in module
php artisan module:make-controller MyController ModuleName

# Create model in module
php artisan module:make-model MyModel ModuleName

# Create migration in module
php artisan module:make-migration create_my_table ModuleName
```

### 10.4 Database Migrations

```bash
# Create migration
php artisan make:migration create_new_table

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Re-run all migrations
php artisan migrate:refresh
```

### 10.5 Testing

#### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/DocumentApprovalTest.php

# Run with coverage
php artisan test --coverage
```

#### Writing Tests

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_document()
    {
        $response = $this->post('/doc_approval/saveForm', [
            'documentType' => 1,
            'departmentOrder' => 'DEPT-001',
            // ... other fields
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('doc_approval_header', [
            'department_order' => 'DEPT-001'
        ]);
    }
}
```

### 10.6 Debugging

#### Laravel Debugbar (Development Only)

```bash
composer require barryvdh/laravel-debugbar --dev
```

#### Log Debugging

```php
// Log messages
\Log::info('Document submitted', ['doc_id' => $docId]);
\Log::error('PDF generation failed', ['error' => $e->getMessage()]);

// Dump and die
dd($variable);

// Dump
dump($variable);
```

#### Query Debugging

```php
// Enable query log
\DB::enableQueryLog();

// Your queries here
$documents = DocumentApprovalModel::where('status', 'pending')->get();

// Get executed queries
dd(\DB::getQueryLog());
```

### 10.7 Optimization

#### Cache Configuration

```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Clear all caches
php artisan optimize:clear
```

#### Database Optimization

```bash
# Add indexes
php artisan make:migration add_indexes_to_tables
```

```php
public function up()
{
    Schema::table('doc_approval_header', function (Blueprint $table) {
        $table->index('transaction_status_id');
        $table->index('company_id');
    });
}
```

#### Query Optimization

```php
// Use eager loading to prevent N+1 queries
$documents = DocumentApprovalModel::with(['company', 'applicant', 'flowSigns'])
    ->where('status', 'pending')
    ->get();

// Use select to load only needed columns
$documents = DocumentApprovalModel::select('id', 'doc_number', 'status')
    ->get();

// Use chunk for large datasets
DocumentApprovalModel::chunk(100, function ($documents) {
    foreach ($documents as $document) {
        // Process document
    }
});
```

---

## 11. Panduan Deployment

### 11.1 Pre-Deployment Checklist

- [ ] All tests passing
- [ ] Code reviewed and approved
- [ ] Database migrations tested
- [ ] Environment variables configured
- [ ] Assets built for production
- [ ] Backup created
- [ ] Rollback plan prepared

### 11.2 Deployment Steps

#### Step 1: Backup

```bash
# Backup database
php artisan backup:run

# Or manual backup
mysqldump -u username -p lid_app > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup files
tar -czf files_backup_$(date +%Y%m%d_%H%M%S).tar.gz storage/app/private
```

#### Step 2: Enable Maintenance Mode

```bash
php artisan down --message="System maintenance in progress" --retry=60
```

#### Step 3: Pull Latest Code

```bash
git fetch origin
git checkout main
git pull origin main
```

#### Step 4: Update Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm ci --production
```

#### Step 5: Run Migrations

```bash
# Check pending migrations
php artisan migrate:status

# Run migrations
php artisan migrate --force
```

#### Step 6: Build Assets

```bash
npm run build
```

#### Step 7: Clear Caches

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Step 8: Restart Services

```bash
# Restart queue workers
sudo supervisorctl restart lid-worker:*

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Clear OPcache (if enabled)
php artisan opcache:clear
```

#### Step 9: Disable Maintenance Mode

```bash
php artisan up
```

#### Step 10: Verify Deployment

```bash
# Check application status
php artisan about

# Run smoke tests
php artisan test --filter SmokeTest

# Check logs
tail -f storage/logs/laravel.log
```

### 11.3 Rollback Procedure

If deployment fails:

```bash
# Enable maintenance mode
php artisan down

# Revert to previous version
git checkout [previous-commit-hash]

# Restore dependencies
composer install --no-dev --optimize-autoloader

# Rollback migrations if needed
php artisan migrate:rollback --force

# Clear caches
php artisan optimize:clear

# Restart services
sudo supervisorctl restart lid-worker:*
sudo systemctl restart php8.2-fpm

# Disable maintenance mode
php artisan up
```

### 11.4 Zero-Downtime Deployment (Advanced)

Using Laravel Envoyer or similar tools:

1. Deploy to new directory
2. Run migrations
3. Build assets
4. Run tests
5. Switch symlink atomically
6. Reload PHP-FPM gracefully

### 11.5 Monitoring Post-Deployment

```bash
# Monitor logs
tail -f storage/logs/laravel.log

# Monitor queue
php artisan queue:monitor

# Check failed jobs
php artisan queue:failed

# Monitor performance
# Use tools like New Relic, Datadog, or Laravel Telescope
```

---

## 12. Troubleshooting

### 12.1 Common Issues

#### Issue: "Class not found" Error

**Cause**: Autoload cache outdated

**Solution**:
```bash
composer dump-autoload
php artisan clear-compiled
php artisan optimize:clear
```

#### Issue: Permission Denied on Storage

**Cause**: Incorrect file permissions

**Solution**:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### Issue: PDF Generation Fails

**Cause**: Missing fonts or PHP memory limit

**Solution**:
```bash
# Install fonts
sudo apt-get install fontconfig libxrender1

# Increase PHP memory limit in php.ini
memory_limit = 512M

# Or in code
ini_set('memory_limit', '512M');
```

#### Issue: Queue Jobs Not Processing

**Cause**: Queue worker not running

**Solution**:
```bash
# Check worker status
sudo supervisorctl status lid-worker:*

# Restart worker
sudo supervisorctl restart lid-worker:*

# Or start manually for testing
php artisan queue:work --tries=3
```

#### Issue: Database Connection Failed

**Cause**: Wrong credentials or server down

**Solution**:
```bash
# Test connection
mysql -h DB_HOST -u DB_USERNAME -p

# Check .env file
cat .env | grep DB_

# Clear config cache
php artisan config:clear
```

#### Issue: Email Not Sending

**Cause**: Wrong SMTP settings or firewall

**Solution**:
```bash
# Test email configuration
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });

# Check logs
tail -f storage/logs/laravel.log

# Verify SMTP settings
# Try different ports: 587, 465, 25
```

### 12.2 Performance Issues

#### Slow Page Load

**Diagnosis**:
```bash
# Enable query logging
php artisan debugbar:enable

# Check slow queries in log
```

**Solution**:
- Add database indexes
- Use eager loading
- Implement caching
- Optimize queries

#### High Memory Usage

**Diagnosis**:
```bash
# Check PHP memory limit
php -i | grep memory_limit

# Monitor using top or htop
top
```

**Solution**:
- Increase PHP memory limit
- Use chunking for large datasets
- Optimize data processing
- Use queue for heavy tasks

### 12.3 Getting Help

#### Check Logs

```bash
# Application logs
tail -f storage/logs/laravel.log

# Web server logs
tail -f /var/log/nginx/error.log
tail -f /var/log/apache2/error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

#### Debug Mode (Development Only)

```env
# .env
APP_DEBUG=true
LOG_LEVEL=debug
```

#### Laravel Telescope (Development Only)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Access at: `/telescope`

---

## Appendix

### A. Glossary

- **Approval Flow**: Alur persetujuan dokumen multi-stage
- **Document Type**: Jenis dokumen dalam sistem approval
- **Module**: Unit fungsional independen dalam aplikasi
- **Queue Job**: Background task yang dijalankan asynchronously
- **Middleware**: Layer pemrosesan request sebelum mencapai controller
- **Eloquent**: ORM (Object-Relational Mapping) Laravel
- **Blade**: Template engine Laravel
- **Artisan**: Command-line interface Laravel

### B. Useful Commands

```bash
# Application
php artisan about                    # Show application info
php artisan serve                    # Start development server
php artisan tinker                   # REPL console

# Cache
php artisan cache:clear              # Clear application cache
php artisan config:clear             # Clear config cache
php artisan route:clear              # Clear route cache
php artisan view:clear               # Clear compiled views

# Database
php artisan migrate                  # Run migrations
php artisan migrate:rollback         # Rollback last migration
php artisan migrate:fresh            # Drop all tables and re-run migrations
php artisan db:seed                  # Run database seeders

# Queue
php artisan queue:work               # Start queue worker
php artisan queue:restart            # Restart queue workers
php artisan queue:failed             # List failed jobs
php artisan queue:retry {id}         # Retry failed job

# Maintenance
php artisan down                     # Enable maintenance mode
php artisan up                       # Disable maintenance mode

# Modules
php artisan module:list              # List all modules
php artisan module:enable {name}     # Enable module
php artisan module:disable {name}    # Disable module
```

### C. External Resources

- Laravel Documentation: https://laravel.com/docs/11.x
- PHP Documentation: https://www.php.net/docs.php
- Laravel Modules: https://github.com/nWidart/laravel-modules
- Bootstrap Documentation: https://getbootstrap.com/docs/5.3/
- Alpine.js Documentation: https://alpinejs.dev/start-here

### D. Contact & Support

**Development Team**: Logisteed IT Department  
**Email**: it@logisteed.id  
**Internal Support**: Ext. 1234

---

**Document Version**: 1.0  
**Last Updated**: November 10, 2025  
**Next Review**: February 10, 2026
