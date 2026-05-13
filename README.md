## Özet

Bu proje, Laravel 11 ve FilamentPHP 5.6 kullanılarak geliştirilmiş kapsamlı bir web uygulamasıdır. Dernekler, vakıflar ve sivil toplum kuruluşları gibi organizasyonların ihtiyaçlarına yönelik olarak tasarlanmıştır. Yönetim paneli üzerinden kolayca içerik yönetimi, bağış kampanyaları, haberler ve duyurular, banka hesapları, gönüllü başvuruları gibi birçok özelliği yönetmek mümkündür. Çok dilli desteği ile geniş bir kitleye hitap edebilmektedir. QR kod oluşturma ve e-posta bildirimleri gibi ek işlevsellikler de sunmaktadır.

## Özellikler

*   **Kapsamlı Yönetim Paneli:** FilamentPHP tabanlı, kullanıcı dostu ve zengin özelliklere sahip yönetim paneli.
*   **İçerik Yönetimi:** Haberler, projeler, sayfalar ve menü öğeleri gibi dinamik içerikleri kolayca oluşturma, düzenleme ve yönetme.
*   **Bağış ve Proje Yönetimi:** Bağış kampanyaları oluşturma, projelere ilişkin bilgileri yönetme ve ilerlemeyi takip etme.
*   **Banka Hesapları Yönetimi:** Organizasyonun banka hesap bilgilerini ekleme ve yönetme.
*   **Gönüllü Başvuruları:** Gönüllü olmak isteyen kişilerin başvurularını toplama ve yönetme.
*   **İletişim Mesajları:** Web sitesi üzerinden gelen iletişim formları aracılığıyla gönderilen mesajları takip etme.
*   **Site Ayarları:** Genel site ayarları, sosyal medya bağlantıları, iletişim bilgileri ve kurumsal kimlik bilgilerini yapılandırma.
*   **Çoklu Dil Desteği:** Türkçe, İngilizce, Arapça ve Rusça gibi birden fazla dil seçeneği ile uluslararası kullanıma uygunluk.
*   **SEO Dostu Yapı:** Sayfalar ve haberler için meta başlık, açıklama gibi SEO ayarları.
*   **QR Kod Entegrasyonu:** Bağış ve ilgili alanlarda QR kod oluşturma yeteneği.
*   **E-posta Bildirimleri:** PHPMailer ile entegre e-posta gönderim sistemi.
*   **Kullanıcı ve Rol Yönetimi:** Yönetim paneli kullanıcılarını ve yetkilerini yönetme.
*   **Medya Yöneticisi:** Görsel ve diğer medya dosyalarını yükleme ve yönetme.

## Gereksinimler

Bu projeyi yerel ortamınızda çalıştırmak için aşağıdaki gereksinimlere ihtiyacınız vardır:

*   PHP 8.2 veya üzeri
*   Composer
*   MySQL veya PostgreSQL gibi bir veritabanı
*   Node.js ve NPM/Yarn (ön yüz bağımlılıkları için)
*   Web sunucusu (Apache, Nginx veya Laravel Herd/Valet gibi)

## Kurulum

Projeyi yerel ortamınızda kurmak ve çalıştırmak için aşağıdaki adımları izleyin:

1.  **Depoyu Klonlayın:**

    ```bash
    git clone https://github.com/Burakgul3085/birliktekardeslik.git
    cd birliktekardeslik
    ```

2.  **Composer Bağımlılıklarını Yükleyin:**

    ```bash
    composer install
    ```

3.  **Ortam Dosyasını Ayarlayın:**

    `.env.example` dosyasını `cp .env.example .env` komutuyla kopyalayın ve veritabanı kimlik bilgilerinizi, e-posta ayarlarınızı ve diğer çevresel değişkenleri düzenleyin. Özellikle `APP_URL` ve veritabanı bağlantı bilgilerini yapılandırmanız gerekmektedir.

4.  **Uygulama Anahtarını Oluşturun:**

    ```bash
    php artisan key:generate
    ```

5.  **Veritabanını Oluşturun ve Göçleri Çalıştırın:**

    `.env` dosyasında veritabanı bağlantınızı yapılandırdıktan sonra veritabanı göçlerini çalıştırın:

    ```bash
    php artisan migrate
    ```

6.  **(İsteğe Bağlı) Demo Verilerini Ekleyin:**

    Eğer demo verileriyle başlamak isterseniz, seed dosyalarını çalıştırabilirsiniz:

    ```bash
    php artisan db:seed
    # veya belirli bir seeder için
    php artisan db:seed --class=DemoContentSeeder
    php artisan db:seed --class=HeaderMenuSeeder
    ```

7.  **Ön Yüz Varlıklarını Yükleyin ve Derleyin:**

    ```bash
    npm install
    npm run dev
    # veya üretim için
    npm run build
    ```

8.  **Depolama Bağlantısını Oluşturun:**

    ```bash
    php artisan storage:link
    ```

9.  **Uygulamayı Çalıştırın:**

    ```bash
    php artisan serve
    ```

    Uygulama genellikle `http://127.0.0.1:8000` adresinde çalışacaktır. Yönetim paneline erişmek için `APP_URL/admin` adresini kullanabilirsiniz. İlk yönetici kullanıcı bilgilerini `database/seeders/DatabaseSeeder.php` veya `database/seeders/DemoContentSeeder.php` dosyalarında bulabilirsiniz (varsa) ya da manuel olarak bir kullanıcı oluşturmanız gerekebilir. (`php artisan make:filament-user` komutu ile yeni bir yönetici kullanıcısı oluşturabilirsiniz.)

## Yönetim Paneli

Projenin ana yönetimi, FilamentPHP tabanlı yönetim paneli aracılığıyla gerçekleştirilir. Yönetim paneline `APP_URL/admin` adresinden erişebilirsiniz. Giriş yaptıktan sonra aşağıdaki ana bölümleri kullanarak sitenizi yönetebilirsiniz:

*   **Gösterge Paneli:** Genel site istatistikleri ve özet bilgiler.
*   **Haberler:** Duyurular ve güncel haberleri ekleme, düzenleme ve silme.
*   **Projeler:** Devam eden veya tamamlanmış projeleri yönetme, detaylar, görseller ve bağış hedefleri belirleme.
*   **Sayfalar:** Statik sayfaları (Hakkımızda, İletişim vb.) oluşturma ve içeriklerini düzenleme.
*   **Menü Öğeleri:** Web sitesi navigasyon menülerini oluşturma ve yönetme.
*   **Banka Hesapları:** Bağışlar için kullanılan banka hesap bilgilerini ekleme ve güncelleme.
*   **Gönüllü Başvuruları:** Gönüllü adaylarının başvurularını inceleme ve yönetme.
*   **İletişim Mesajları:** Kullanıcılar tarafından gönderilen iletişim formlarını görüntüleme.
*   **Ayarlar:** Genel site ayarları, sosyal medya bağlantıları, logo, iletişim bilgileri, SEO ayarları ve daha fazlasını yapılandırma.
*   **Kullanıcı Yönetimi:** Yönetim paneli kullanıcılarını ve yetkilerini yönetme.

## Çoklu Dil Desteği

Proje, birden fazla dil desteği sunmaktadır. `lang` dizini altında bulunan dil dosyaları (`ar`, `en`, `ru`, `tr` gibi) aracılığıyla site içeriğini farklı dillere çevirebilirsiniz. Yönetim paneli üzerinden de bazı içeriklerin çevirileri yapılabilmektedir (proje ve haberler için i18n alanları görüldü).

## Katkıda Bulunma

Projenin gelişimine katkıda bulunmak isterseniz, lütfen bir Pull Request (Çekme İsteği) göndermekten çekinmeyin. Her türlü katkı memnuniyetle karşılanır.

## Lisans

Bu proje MIT Lisansı altında lisanslanmıştır. Daha fazla bilgi için `LICENSE` dosyasına bakabilirsiniz (proje içinde `LICENSE` dosyası yoksa, bu bilgi doğrulanmalıdır).
