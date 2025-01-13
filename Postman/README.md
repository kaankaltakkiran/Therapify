# Therapify API Dokümantasyonu

Bu klasör, Therapify API uç noktalarını test etmek ve etkileşimde bulunmak için Postman koleksiyonunu içerir.

## Başlangıç

1. [Postman'ı yükleyin](https://www.postman.com/downloads/)
2. Koleksiyon dosyasını (`Therapify.postman_collection.json`) Postman'a içe aktarın
## Mevcut Uç Noktalar

### Kimlik Doğrulama

#### 1. Giriş
- **Metod**: POST
- **Uç Nokta**: `/api/auth.php`
- **Gövde**: JSON
  ```json
  {
    "method": "login",
    "email": "your-email@example.com",
    "password": "your-password"
  }
  ```
- **Yanıt**: JWT token ve kullanıcı bilgilerini döndürür

#### 2. Kullanıcı Kaydı
- **Metod**: POST
- **Uç Nokta**: `/api/auth.php`
- **Gövde**: Form-data
  - method: "register"
  - first_name (ad)
  - last_name (soyad)
  - email (e-posta)
  - password (şifre)
  - address (adres)
  - phone_number (telefon numarası)
  - birth_of_date (doğum tarihi)
  - user_img (kullanıcı resmi - dosya)
- **Yanıt**: Başarı mesajı ve kullanıcı ID'si

#### 3. Terapist Kaydı
- **Metod**: POST
- **Uç Nokta**: `/api/auth.php`
- **Gövde**: Form-data
  - method: "therapist-register"
  - (tüm kullanıcı alanları)
  - title (unvan)
  - about_text (hakkında metni)
  - session_fee (seans ücreti)
  - session_duration (seans süresi)
  - languages_spoken (konuşulan diller - JSON dizisi)
  - video_session_available (video seans mevcut mu)
  - face_to_face_session_available (yüz yüze seans mevcut mu)
  - office_address (ofis adresi)
  - education (eğitim)
  - license_number (lisans numarası)
  - experience_years (deneyim yılı)
  - specialties (uzmanlık alanları - JSON dizisi)
  - cv_file (CV dosyası)
  - diploma_file (diploma dosyası)
  - license_file (lisans dosyası)
- **Yanıt**: Başarı mesajı ve kullanıcı ID'si

### Yönetici

#### 1. Terapist Başvurularını Al
- **Metod**: POST
- **Uç Nokta**: `/api/admin.php`
- **Gövde**: JSON
  ```json
  {
    "method": "get-therapist-applications"
  }
  ```
- **Yanıt**: Tüm terapist başvurularının listesi

#### 2. Başvuru Durumunu Güncelle
- **Metod**: POST
- **Uç Nokta**: `/api/admin.php`
- **Gövde**: JSON
  ```json
  {
    "method": "update-application-status",
    "application_id": 1,
    "status": "approved",
    "admin_notes": "Notlarınızı buraya yazın"
  }
  ```
- **Yanıt**: Başarı mesajı

#### 3. Bekleyen Başvuru Sayısını Al
- **Metod**: POST
- **Uç Nokta**: `/api/admin.php`
- **Gövde**: JSON
  ```json
  {
    "method": "get-pending-count"
  }
  ```
- **Yanıt**: Bekleyen başvuruların sayısı

### Terapist

#### 1. Onaylanmış Terapistleri Al
- **Metod**: POST
- **Uç Nokta**: `/api/therapist.php`
- **Gövde**: JSON
  ```json
  {
    "method": "get-approved-therapists"
  }
  ```
- **Yanıt**: Onaylanmış terapistlerin detaylı listesi

## Test İpuçları

1. JWT token almak için kullanıcı kaydı veya girişi ile başlayın
2. Korumalı uç noktalar için, Authorization başlığına token ekleyin:
   ```
   Authorization: Bearer your-jwt-token
   ```
3. Dosya yüklemelerini test ederken, dosyaların sisteminizde var olduğundan emin olun
4. Hata ayıklama için yanıt durum kodlarını ve mesajlarını kontrol edin

## Sık Karşılaşılan Sorunlar

1. **Dosya Yükleme Hatası**: Dosya boyutunun sınırlar içinde ve dosya tipinin izin verilen türde olduğundan emin olun
2. **Kimlik Doğrulama Hatası**: E-posta ve şifrenin doğru olduğunu kontrol edin
3. **JWT Token Geçersiz**: Token süresi dolmuş olabilir, tekrar giriş yapmayı deneyin
4. **CORS Sorunları**: API sunucunuzun alan adınızdan gelen isteklere izin verdiğinden emin olun 