# Birlikte Kardeşlik Derneği Web Platformu

Bu proje, dernek web sitesi ve yönetim panelini tek bir çatı altında toplayan, tamamen dinamik ve admin panelden yönetilebilir bir Laravel 11 uygulamasıdır.

## Teknoloji Yığını

- `Laravel 11`
- `Filament` (yönetim paneli)
- `Tailwind CSS` + `Alpine.js`
- `MySQL` (yerel ve canlı ortam desteği)
- `PHPMailer` (SMTP üzerinden e-posta gönderimi)

## Öne Çıkan Özellikler

- Genel ayarların (site başlığı, logo, favicon, iletişim, sosyal medya vb.) panelden yönetimi
- Dinamik menü, hero slider, sayfalar, projeler, haberler ve banka hesapları
- Bağış sayfası ve IBAN kopyalama akışı
- İletişim formu:
  - Veritabanına kayıt
  - Yönetim paneline düşme
  - Yöneticiye bildirim e-postası
  - Başvuru sahibine otomatik bilgilendirme e-postası
- Gönüllü ol formu:
  - Dinamik tercih listesi (admin panelden yönetilir)
  - Veritabanına kayıt ve panelde görüntüleme
  - Yönetici cevabı ile adaya e-posta gönderimi
- Admin aktivite logları:
  - Giriş/çıkış, gezinme, model değişiklikleri
  - Filtreleme ve dışa aktarma
- Rol bazlı yetkilendirme:
  - `super_admin`
  - `editor`
  - `viewer`
- Türkçeleştirilmiş yönetim paneli ve kullanıcı arayüzü

## Kurulum

### 1) Depoyu klonla

```bash
git clone https://github.com/Burakgul3085/birliktekardeslik.git
cd birliktekardeslik
```

### 2) Bağımlılıkları yükle

```bash
composer install
npm install
```

### 3) Ortam dosyası ve uygulama anahtarı

```bash
cp .env.example .env
php artisan key:generate
```

### 4) Veritabanı ayarları

`.env` içinde MySQL bilgilerini düzenleyin:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=birliktekardeslik
DB_USERNAME=root
DB_PASSWORD=root
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### 5) Migration ve depolama linki

```bash
php artisan migrate
php artisan storage:link
```

### 6) Frontend derleme

```bash
npm run dev
```

### 7) Uygulamayı çalıştırma

```bash
php artisan serve
```

## Yönetim Paneli

- URL: `http://127.0.0.1:8000/admin`
- İlk admin kullanıcı için:

```bash
php artisan make:filament-user
```

## E-posta (PHPMailer) Ayarları

`.env` dosyasında aşağıdaki alanları doldurun:

```env
PHPMAILER_HOST=smtp.gmail.com
PHPMAILER_PORT=587
PHPMAILER_ENCRYPTION=tls
PHPMAILER_USERNAME=ornek@gmail.com
PHPMAILER_PASSWORD=uygulama_sifresi
PHPMAILER_FROM_ADDRESS=ornek@gmail.com
PHPMAILER_FROM_NAME="Birlikte Kardeşlik Derneği"
```

> Not: Gmail için uygulama şifresi kullanılması önerilir.

## Dinamik Olarak Panelden Yönetilen Başlıca Alanlar

- Üst bar iletişim bilgileri (telefon, e-posta, adres)
- Sosyal medya bağlantıları (Instagram, YouTube, TikTok, Facebook, X)
- Mail şablonlarındaki kurumsal bilgiler ve logo
- Gönüllülük alan tercihleri
- KVKK ve gönüllü aydınlatma metinleri
- Banka hesapları ve bağış sayfası verileri

## Test

```bash
php artisan test
```

## Lisans

Bu proje `MIT` lisansı ile lisanslanmıştır.
