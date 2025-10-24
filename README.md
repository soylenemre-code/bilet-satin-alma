                                                 🚌 Bilet Satın Alma Platformu

Bu proje,backend olarak php,frontend olarak html ve css ve database olarak sqllite kullanılarak geliştirilmiştir.Siteyi docker üzerinden kurduktan sonra karşınıza çıkan ana sayfada hesabınız varsa giril yap butonu ile giriş yapabilirsiniz veya kayıt ol butonu vaıstasıyla kayıt olabilirsiniz.Sitede herhangibir hesbaınız olmadan firmalar veya sistem yöneticisi tarafından girilen seferleri tarihe göre arayabilirsiniz ve boş koltukları görüntüleyebilirsiniz herhangibir koltuk satınalmak istediğinizde otomatik olarak sizi login(girişyap)sayfasına yönlendirecektir. 
  GÖREVİN AMACI:
Kalkış-varış şehirlerine göre sefer arama, koltuk seçimi, kupon kullanımı ve PDF bilet çıktısı gibi işlemleri destekleyen, veritabanı tabanlı bir otobüs bileti satış otomasyonu geliştirmek.

ÖNEMLİ BİLGİLENDİRME:Yeni firma ekleme Firma admini oluşturma ve firmaya tanımlmalamak için 39-40ve41.satırlardaki açıklamayabakınız!!! Firmaadmin rolü olan herhangibir kullanıcı eğer herhangibir firma ile ilişkili değilse sefer ekleme ekranında hata alırsınız sebebi company_id ve user tablolarının ilişkilendirilerek sefer oluşturma listeleme vs işlemlerinin yapılmasından kaynaklıdır.
🔐 Varsayılan Giriş Bilgileri
Rol	E-posta	Şifre
Admin1	yeniadmin@gmail.com şifre:yeniadmin
Admin2	yavuzlar@admin.com şifre:yavuzlar
Kullanıcı: test@test.com şifre:test1234

Firma Admin1: atur@gmail.com şifre:atur	
Firma Admin 2: sibervatantur@gmail.com

               KURULUM VE BAŞLANGIÇ:
⚙️ Kurulum ve Çalıştırma (Docker)
1. Gereksinimler

    Docker Desktop

    (Windows için) WSL 2

2. Projeyi İndirme
git clone [DEPO ADRESİNİZ] bilet-satin-alma
cd bilet-satin-alma  //cihazınızın console ekranında bu şekilde localinize kopyalayabilirsiniz.

3.Docker Üzerinden Başlatma:
docker compose up -d
yazarak docker üzerinden repoyu çalıştırabilirsiniz.

4.Erişim:Docker üzerinden çalıştırdıktan sonra tarayıcınızda 127.0.0.1:8000 yazarak erişebilirsiniz.

👥 Kullanıcı Rolleri ve Yetkilendirme

Platformda üç ana kullanıcı rolü bulunmaktadır:
🛠️ Sistem Yöneticisi (admin)
   !!!!ÖNEMLİ NOT:Sistemde sadece admin kullanıcısı yeni firma oluşturabilir ve yeni firmaadminleri oluşturabilir.
   Firma Ekleme işlemi:Admin kullanıcısı giriş yaptıktan sonra http://127.0.0.1:8000/adminpanel/firmsadd.php üzerinden(yandaki navigasyon bar üzerinden erişilebilir) firma ismi girerek firma oluşturabilir.
   FİRMAADMİN'i oluşturma Ve Firmaya Atama:"FİRMAADMİN" rolü kullanıcıyı oluşturma için giriş yaptıktan sonra "Kulllanıcıları Listele"http://127.0.0.1:8000/adminpanel/users.php butoonuna tıklanır ve açılan sayfadan sol üstteki "Yeni Kullanıcı Ekle" butonuna tıklanarak yeni firmadmin rolü olan kullanıcılar oluşturabilirsiniz.Adminpanel ana sayfadaki "Firma Admini Tanımlama" sayfasından(http://127.0.0.1:8000/adminpanel/assign_company.php) firmadmin rolü olan kullanıcıları istediğiniz firmaya atayabilirsiniz.
   
    Yeni firmalar ekleme, aktifleştirme/pasifleştirme

    Kullanıcıları firma yöneticisi olarak atama/geri alma

    Tüm kullanıcıları listeleme

    Global veya firma bazlı kupon yönetimi
Kullanıcı:

    Register.php'den kayıt olduktan sonra sistem tarafından 900 bakiye otomatik olarak tanımlanır.

    Kullanıcı giriş yaptıktan sonra /user/index.php'den seferleri arayabilir profil bilgilerini güncelleyebilir.

    Kullanıcı seferler sayfasından seçtiği seferin boş ve dolu koltukları görüntüleyebilir ve uygun olanı satınalabilir(satınalma sırasında kupon kodu girererk indirimli alabilir.
    Biletlerim sayfasından Aktif ve geçmiş seferleri görüntüleyebilme,pdf formatında bilet indirme(not:iptal işlemi sefer saatine 1 saatten az zaman kalıysa gerçekleştirilemez)
    Profil bilgilerini güncelleme

🏢 Firma Yöneticisi (firm_admin)

    Kendi firmasına ait seferleri ekleme, düzenleme, silme

    Kupon oluşturma, aktifleştirme/pasifleştirme, silme





