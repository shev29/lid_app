# Logisteed LID Application

<p align="center">
<img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white" alt="PHP 8.2+">
<img src="https://img.shields.io/badge/Laravel-11.9-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel 11.9">
<img src="https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white" alt="MySQL">
<img src="https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=flat&logo=bootstrap&logoColor=white" alt="Bootstrap 5">
</p>

## 📋 Tentang Aplikasi

Logisteed LID Application adalah sistem enterprise berbasis web yang dirancang untuk mengelola proses bisnis logistik dan procurement di Logisteed Indonesia. Aplikasi ini mengotomatisasi alur persetujuan dokumen, proses pembelian, dan manajemen aplikasi internal dengan arsitektur modular yang scalable.

### ✨ Fitur Utama

- 📄 **Sistem Persetujuan Dokumen** - Multi-stage approval workflow dengan notifikasi email
- 🛒 **Procurement & Purchasing** - Manajemen PO, vendor, dan inspection forms
- 🔐 **Manajemen Aplikasi** - Storage management dan access control
- 🚢 **Integrasi CEISA H2H** - Otomasi proses kepabeanan
- 📊 **Pelaporan Real-time** - Dashboard dan analytics
- 📧 **Email Otomatis** - Notifikasi dan reminder terjadwal
- 🖨️ **PDF Generation** - Otomatis generate dokumen PDF dari template Excel
- 🔒 **Keamanan Tingkat Enterprise** - Multi-layer security dan audit trail

## 🏗️ Arsitektur

Aplikasi menggunakan **Modular Monolith** architecture dengan 5 modul utama:

```
┌─────────────────────────────────────────┐
│           Home Module                    │
│           (Dashboard)                    │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│      DocumentApproval Module             │
│   (Approval Workflow & Documents)        │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│   ProcurementPurchasing Module           │
│   (PO, Vendor, Inspection)               │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│   ApplicationManager Module              │
│   (App & Storage Management)             │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│        CeisaH2h Module                   │
│     (Customs Integration)                │
└─────────────────────────────────────────┘
```

## 🔧 Teknologi Stack

### Backend
- **Framework**: Laravel 11.9 (PHP 8.2+)
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Queue**: Laravel Queue (Database driver)
- **PDF Processing**: mPDF, DomPDF, FPDI, TCPDF
- **Excel Processing**: PhpSpreadsheet, Maatwebsite Excel
- **Email**: Symfony Mailer
- **Image Processing**: Intervention Image
- **QR Code**: Simple QRCode

### Frontend
- **UI Framework**: Bootstrap 5.x
- **JavaScript**: Alpine.js, jQuery 3.7
- **DataTables**: DataTables.js with Bootstrap integration
- **Components**: Select2, SweetAlert2, Virtual Select
- **Build Tool**: Vite 5.3

### Development Tools
- **Composer**: Dependency management
- **NPM**: Package management
- **Laravel Pint**: Code style fixer
- **PHPUnit**: Testing framework
- **Laravel Sail**: Docker environment (optional)

## 📦 Instalasi

### Persyaratan Sistem

- PHP >= 8.2 dengan extensions: BCMath, Ctype, cURL, DOM, Fileinfo, GD, JSON, Mbstring, OpenSSL, PDO, XML, Zip
- MySQL >= 5.7 atau MariaDB >= 10.3
- Composer >= 2.0
- Node.js >= 18.x
- NPM >= 9.x

### Langkah Instalasi

1. **Clone repository**
```bash
git clone https://github.com/shev29/lid_app.git
cd lid_app
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Setup environment**
```bash
cp .env.example .env  # Buat dari template jika tersedia
php artisan key:generate
```

4. **Konfigurasi database**

Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lid_app
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Jalankan migrasi**
```bash
php artisan migrate
```

6. **Setup storage**
```bash
php artisan storage:link
mkdir -p storage/app/private/{template,doc_approval,fonts,temp}
chmod -R 775 storage bootstrap/cache
```

7. **Build assets**
```bash
npm run build
```

8. **Jalankan aplikasi**
```bash
# Development
php artisan serve

# Production (dengan web server: Nginx/Apache)
```

## 🚀 Deployment

### Production Deployment

1. **Setup Queue Worker**
```bash
# Using Supervisor
sudo nano /etc/supervisor/conf.d/lid-worker.conf
```

2. **Setup Cron Jobs**
```bash
crontab -e
# Tambahkan:
* * * * * cd /var/www/lid_app && php artisan schedule:run >> /dev/null 2>&1
```

3. **Optimize untuk Production**
```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Lihat [TECHNICAL_DOCUMENTATION.md](TECHNICAL_DOCUMENTATION.md) untuk panduan deployment lengkap.

## 📖 Dokumentasi

Dokumentasi lengkap tersedia di:
- **[TECHNICAL_DOCUMENTATION.md](TECHNICAL_DOCUMENTATION.md)** - Dokumentasi teknis lengkap (1956 baris)
  - Arsitektur sistem
  - Panduan instalasi detail
  - Struktur database
  - API documentation
  - Security best practices
  - Development guidelines
  - Deployment procedures
  - Troubleshooting guide

## 🔐 Keamanan

- ✅ Session-based authentication dengan token validation
- ✅ CSRF protection pada semua forms
- ✅ XSS prevention dengan input sanitization
- ✅ SQL injection prevention via Eloquent ORM
- ✅ File upload validation dan security
- ✅ Password hashing dengan BCrypt
- ✅ API key authentication
- ✅ Rate limiting
- ✅ Audit logging
- ✅ Automated backups

## 🧪 Testing

```bash
# Jalankan semua tests
php artisan test

# Dengan coverage
php artisan test --coverage

# Test spesifik
php artisan test tests/Feature/DocumentApprovalTest.php
```

## 📊 Modul & Fitur

### 1. Document Approval
- Multi-stage approval workflow
- Document type management (Order Form, Application, PO, Inspection, Comparison)
- PDF generation dari Excel templates
- Email notifications
- Digital signature flow
- Document history & audit trail

### 2. Procurement & Purchasing
- Purchase requisition management
- Purchase order processing
- Vendor management
- Inspection forms
- Freight price requests
- Excel import/export

### 3. Application Manager
- Internal application listing
- Storage management
- File upload/download
- Access control
- Usage monitoring

### 4. CEISA H2H Integration
- Automated submission ke Bea Cukai
- Response handling
- Error tracking
- Retry mechanism

### 5. Home Dashboard
- Overview statistics
- Quick access menu
- Notifications
- User activity

## 🛠️ Development

### Coding Standards

Aplikasi menggunakan PSR-12 coding standard:

```bash
# Format code
./vendor/bin/pint
```

### Git Workflow

```bash
# Create feature branch
git checkout -b feature/your-feature

# Commit dengan format:
# feat: new feature
# fix: bug fix
# docs: documentation
# style: formatting
# refactor: code refactoring
# test: add tests

git commit -m "feat: add new approval workflow"
```

### Creating New Module

```bash
php artisan module:make ModuleName
php artisan module:enable ModuleName
```

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'feat: add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Create Pull Request

## 📝 License

Aplikasi ini adalah proprietary software milik Logisteed Indonesia.

## 👥 Tim Pengembangan

**Development Team**: Logisteed IT Department  
**Email**: it@logisteed.id  

## 📞 Support

Untuk bantuan teknis:
- **Email**: it@logisteed.id
- **Internal Support**: Extension 1234

---

## 📚 Quick Links

- [Technical Documentation](TECHNICAL_DOCUMENTATION.md) - Dokumentasi teknis lengkap
- [Laravel Documentation](https://laravel.com/docs/11.x) - Framework documentation
- [Bootstrap Documentation](https://getbootstrap.com/docs/5.3/) - UI framework
- [Alpine.js Documentation](https://alpinejs.dev/) - JavaScript framework

## 🔄 Recent Updates

### Version 1.0 (Current)
- ✅ Modular architecture dengan 5 modul
- ✅ Document approval system dengan multi-stage workflow
- ✅ Procurement & purchasing management
- ✅ PDF generation dari Excel templates
- ✅ Email notifications & reminders
- ✅ CEISA H2H integration
- ✅ Storage & application management
- ✅ Comprehensive security features
- ✅ Full technical documentation

---

<p align="center">
Made with ❤️ by Logisteed IT Team
</p>
