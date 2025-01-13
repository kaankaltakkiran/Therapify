<template>
  <q-page class="q-pa-md">
    <div class="container q-py-lg">
      <h1 class="text-h4 text-primary q-mb-lg text-center">{{ $t('Gizlilik Politikası') }}</h1>

      <div class="privacy-content q-gutter-y-lg">
        <div class="section">
          <h2 class="text-h5 q-mb-md">{{ $t('1. Veri Toplama ve Kullanım') }}</h2>
          <p>
            {{ $t('Therapify olarak, kullanıcılarımızın gizliliğini korumayı taahhüt ediyoruz. Bu politika, hangi verileri topladığımızı ve nasıl kullandığımızı açıklar.') }}
          </p>
          <ul>
            <li>{{ $t('Kişisel bilgiler (ad, soyad, e-posta)') }}</li>
            <li>{{ $t('İletişim bilgileri') }}</li>
            <li>{{ $t('Profil bilgileri') }}</li>
            <li>{{ $t('Kullanım verileri') }}</li>
          </ul>
        </div>

        <div class="section">
          <h2 class="text-h5 q-mb-md">{{ $t('2. Veri Güvenliği') }}</h2>
          <p>
            {{ $t('Verilerinizi korumak için endüstri standardı güvenlik önlemleri kullanıyoruz:') }}
          </p>
          <ul>
            <li>{{ $t('SSL şifreleme') }}</li>
            <li>{{ $t('Güvenli veri depolama') }}</li>
            <li>{{ $t('Düzenli güvenlik güncellemeleri') }}</li>
            <li>{{ $t('Erişim kontrolü') }}</li>
          </ul>
        </div>

        <div class="section">
          <h2 class="text-h5 q-mb-md">{{ $t('3. Çerezler ve İzleme') }}</h2>
          <p>
            {{ $t('Hizmetlerimizi iyileştirmek için çerezler ve benzer teknolojiler kullanıyoruz:') }}
          </p>
          <ul>
            <li>{{ $t('Oturum çerezleri') }}</li>
            <li>{{ $t('Tercih çerezleri') }}</li>
            <li>{{ $t('Analitik çerezler') }}</li>
          </ul>
        </div>

        <div class="section">
          <h2 class="text-h5 q-mb-md">{{ $t('4. Veri Paylaşımı') }}</h2>
          <p>
            {{ $t('Verilerinizi üçüncü taraflarla yalnızca aşağıdaki durumlarda paylaşırız:') }}
          </p>
          <ul>
            <li>{{ $t('Yasal zorunluluk durumunda') }}</li>
            <li>{{ $t('Açık rızanız olduğunda') }}</li>
            <li>{{ $t('Hizmet sağlayıcılarımızla (veri işleme)') }}</li>
          </ul>
        </div>

        <div class="section">
          <h2 class="text-h5 q-mb-md">{{ $t('5. Kullanıcı Hakları') }}</h2>
          <p>
            {{ $t('KVKK kapsamında aşağıdaki haklara sahipsiniz:') }}
          </p>
          <ul>
            <li>{{ $t('Verilerinize erişim hakkı') }}</li>
            <li>{{ $t('Verilerinizi düzeltme hakkı') }}</li>
            <li>{{ $t('Verilerinizin silinmesini talep etme hakkı') }}</li>
            <li>{{ $t('Veri işlemeye itiraz etme hakkı') }}</li>
          </ul>
        </div>

        <div class="section">
          <h2 class="text-h5 q-mb-md">{{ $t('İletişim') }}</h2>
          <p>
            {{ $t('Gizlilik politikamızla ilgili sorularınız için:') }}
          </p>
          <ul>
            <li>{{ $t('E-posta') }}: privacy@therapify.com</li>
            <li>{{ $t('Telefon') }}: +90 (555) 123-4567</li>
            <li>{{ $t('Adres') }}: Terapi Sokak No: 123, Ruh Sağlığı Mahallesi, İstanbul</li>
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