<template>
  <q-page class="q-pa-md">
    <div class="container q-py-lg">
      <h1 class="text-h4 text-primary q-mb-lg text-center">Gizlilik Politikası</h1>

      <div class="privacy-content q-gutter-y-lg">
        <div class="section">
          <h2 class="text-h5 q-mb-md">1. Veri Toplama ve Kullanım</h2>
          <p>
            Therapify olarak, kullanıcılarımızın gizliliğini korumayı taahhüt ediyoruz. Bu politika,
            hangi verileri topladığımızı ve nasıl kullandığımızı açıklar.
          </p>
          <ul>
            <li>Kişisel bilgiler (ad, soyad, e-posta)</li>
            <li>İletişim bilgileri</li>
            <li>Profil bilgileri</li>
            <li>Kullanım verileri</li>
          </ul>
        </div>

        <div class="section">
          <h2 class="text-h5 q-mb-md">2. Veri Güvenliği</h2>
          <p>
            Verilerinizi korumak için endüstri standardı güvenlik önlemleri kullanıyoruz:
          </p>
          <ul>
            <li>SSL şifreleme</li>
            <li>Güvenli veri depolama</li>
            <li>Düzenli güvenlik güncellemeleri</li>
            <li>Erişim kontrolü</li>
          </ul>
        </div>

        <div class="section">
          <h2 class="text-h5 q-mb-md">3. Çerezler ve İzleme</h2>
          <p>
            Hizmetlerimizi iyileştirmek için çerezler ve benzer teknolojiler kullanıyoruz:
          </p>
          <ul>
            <li>Oturum çerezleri</li>
            <li>Tercih çerezleri</li>
            <li>Analitik çerezler</li>
          </ul>
        </div>

        <div class="section">
          <h2 class="text-h5 q-mb-md">4. Veri Paylaşımı</h2>
          <p>
            Verilerinizi üçüncü taraflarla yalnızca aşağıdaki durumlarda paylaşırız:
          </p>
          <ul>
            <li>Yasal zorunluluk durumunda</li>
            <li>Açık rızanız olduğunda</li>
            <li>Hizmet sağlayıcılarımızla (veri işleme)</li>
          </ul>
        </div>

        <div class="section">
          <h2 class="text-h5 q-mb-md">5. Kullanıcı Hakları</h2>
          <p>
            KVKK kapsamında aşağıdaki haklara sahipsiniz:
          </p>
          <ul>
            <li>Verilerinize erişim hakkı</li>
            <li>Verilerinizi düzeltme hakkı</li>
            <li>Verilerinizin silinmesini talep etme hakkı</li>
            <li>Veri işlemeye itiraz etme hakkı</li>
          </ul>
        </div>

        <div class="section">
          <h2 class="text-h5 q-mb-md">6. İletişim</h2>
          <p>
            Gizlilik politikamızla ilgili sorularınız için:
          </p>
          <ul>
            <li>E-posta: privacy@therapify.com</li>
            <li>Telefon: +90 (555) 123-4567</li>
            <li>Adres: Terapi Sokak No: 123, Ruh Sağlığı Mahallesi, İstanbul</li>
          </ul>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup lang="ts">
// No script needed for now
</script>

<style lang="scss" scoped>
.container {
  max-width: 800px;
  margin: 0 auto;
}

.privacy-content {
  .section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba($primary, 0.1);

    h2 {
      color: $primary;
      font-weight: 600;
    }

    ul {
      list-style: none;
      padding-left: 1rem;
      margin: 1rem 0;

      li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.5rem;
        color: $grey-8;

        &:before {
          content: '•';
          color: $primary;
          position: absolute;
          left: 0;
          font-weight: bold;
        }
      }
    }

    p {
      color: $grey-8;
      line-height: 1.6;
    }
  }
}
</style> 