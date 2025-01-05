<template>
  <q-page>
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container q-pa-md">
        <div class="row items-center">
          <div class="col-12 col-md-6">
            <h1 class="text-h2 text-weight-bold q-mb-md text-white">
              Ruhsal İyilik Yolculuğunuza Başlayın
            </h1>
            <p class="text-h6 q-mb-lg text-white text-weight-medium" style="opacity: 0.95">
              Yapay zeka destekli eşleştirme sistemiyle, kişiselleştirilmiş ruh sağlığı desteği için
              uzman terapistlerle bağlantı kurun.
            </p>
            <div class="q-gutter-md">
              <q-btn
                color="white"
                text-color="primary"
                size="lg"
                label="Hemen Başla"
                to="/auth/register"
                push
                class="hero-btn"
              />
              <q-btn
                outline
                color="white"
                size="lg"
                label="Daha Fazla Bilgi"
                @click="scrollToServices"
                push
                class="hero-btn"
              />
            </div>
          </div>
          <div class="col-12 col-md-6">
            <q-img src="/images/hero-illustration.svg" />
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
      <div class="container q-pa-md">
        <h2 class="text-h3 text-center q-mb-lg">Hizmetlerimiz</h2>
        <div class="row q-col-gutter-md">
          <!-- AI Matching -->
          <div class="col-12 col-md-4">
            <q-card class="service-card">
              <q-card-section>
                <div class="text-h6">Yapay Zeka Eşleştirmesi</div>
                <p class="text-grey-8">
                  Gelişmiş yapay zeka sistemimiz, ihtiyaç ve tercihlerinizi analiz ederek size en
                  uygun terapisti bulur.
                </p>
              </q-card-section>
            </q-card>
          </div>

          <!-- Online Sessions -->
          <div class="col-12 col-md-4">
            <q-card class="service-card">
              <q-card-section>
                <div class="text-h6">Güvenli Online Seanslar</div>
                <p class="text-grey-8">
                  Evinizin rahatlığında, güvenli görüntülü görüşme ve mesajlaşma yoluyla
                  terapistinizle bağlantı kurun.
                </p>
              </q-card-section>
            </q-card>
          </div>

          <!-- Progress Tracking -->
          <div class="col-12 col-md-4">
            <q-card class="service-card">
              <q-card-section>
                <div class="text-h6">İlerleme Takibi</div>
                <p class="text-grey-8">
                  Yapay zeka destekli içgörüler ve ilerleme raporlarıyla ruh sağlığı yolculuğunuzu
                  takip edin.
                </p>
              </q-card-section>
            </q-card>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works bg-grey-2">
      <div class="container q-pa-md">
        <h2 class="text-h3 text-center q-mb-lg">Nasıl Çalışır?</h2>
        <div class="row q-col-gutter-md">
          <div class="col-12 col-md-3">
            <div class="step-card text-center">
              <div class="step-number">1</div>
              <h6 class="text-h6">Kayıt Ol</h6>
              <p class="text-grey-8">Hesabınızı oluşturun ve kısa bir değerlendirme yapın</p>
            </div>
          </div>
          <div class="col-12 col-md-3">
            <div class="step-card text-center">
              <div class="step-number">2</div>
              <h6 class="text-h6">Eşleştirme</h6>
              <p class="text-grey-8">Yapay zekamız sizi uygun terapistlerle eşleştirir</p>
            </div>
          </div>
          <div class="col-12 col-md-3">
            <div class="step-card text-center">
              <div class="step-number">3</div>
              <h6 class="text-h6">Randevu Al</h6>
              <p class="text-grey-8">Size uygun bir zamanda ilk seansınızı planlayın</p>
            </div>
          </div>
          <div class="col-12 col-md-3">
            <div class="step-card text-center">
              <div class="step-number">4</div>
              <h6 class="text-h6">Terapiye Başla</h6>
              <p class="text-grey-8">Daha iyi bir ruh sağlığı için yolculuğunuza başlayın</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- For Therapists -->
    <section class="therapist-section">
      <div class="container q-pa-md">
        <div class="row items-center">
          <div class="col-12 col-md-6">
            <q-img src="/images/therapist-illustration.svg" />
          </div>
          <div class="col-12 col-md-6">
            <h2 class="text-h3 q-mb-md">Terapistler İçin</h2>
            <p class="text-h6 q-mb-lg text-grey-8">
              Uzmanlığınıza uygun danışanlarla buluşmak ve pratiğinizi genişletmek için
              platformumuza katılın.
            </p>
            <div class="features q-mb-lg">
              <div class="feature-item q-mb-sm">
                <q-icon name="check_circle" color="positive" size="sm" />
                <span class="q-ml-sm">Yapay zeka destekli danışan eşleştirmesi</span>
              </div>
              <div class="feature-item q-mb-sm">
                <q-icon name="check_circle" color="positive" size="sm" />
                <span class="q-ml-sm">Esnek planlama</span>
              </div>
              <div class="feature-item q-mb-sm">
                <q-icon name="check_circle" color="positive" size="sm" />
                <span class="q-ml-sm">Güvenli ödeme sistemi</span>
              </div>
              <div class="feature-item q-mb-sm">
                <q-icon name="check_circle" color="positive" size="sm" />
                <span class="q-ml-sm">Mesleki gelişim kaynakları</span>
              </div>
            </div>
            <q-btn
              color="secondary"
              size="lg"
              label="Terapist Olarak Katıl"
              to="/auth/register?type=therapist"
              push
            />
          </div>
        </div>
      </div>
    </section>
  </q-page>
</template>

<script setup lang="ts">
const scrollToServices = () => {
  const element = document.getElementById('services')
  element?.scrollIntoView({ behavior: 'smooth' })
}
</script>

<style lang="scss" scoped>
.hero-section {
  min-height: 60vh;
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, $primary 0%, darken($primary, 15%) 100%);
  color: white;
  padding: 2rem 0;
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0) 100%);
    z-index: 1;
  }

  .container {
    position: relative;
    z-index: 2;
  }

  h1 {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  p {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  }

  .hero-btn {
    font-weight: 600;
    border-width: 2px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
  }
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.service-card {
  height: 100%;
  transition: transform 0.3s ease;

  &:hover {
    transform: translateY(-5px);
  }
}

.step-card {
  padding: 1.5rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);

  .step-number {
    width: 36px;
    height: 36px;
    background: $primary;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    font-size: 1.1rem;
    font-weight: bold;
  }
}

.feature-item {
  display: flex;
  align-items: center;
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

h1.text-h2 {
  font-size: 2.5rem;
  line-height: 1.2;
}

h2.text-h3 {
  font-size: 2rem;
  margin-bottom: 2rem;
}

section {
  padding: 3rem 0;

  &.services-section,
  &.how-it-works,
  &.therapist-section {
    padding: 4rem 0;
  }
}

// Responsive adjustments
@media (max-width: 767px) {
  .hero-section {
    min-height: auto;
    padding: 3rem 0;
    text-align: center;
  }

  section {
    padding: 2rem 0;
  }

  .step-card {
    margin-bottom: 1rem;
  }

  h1.text-h2 {
    font-size: 2rem;
  }

  h2.text-h3 {
    font-size: 1.75rem;
  }

  .q-img {
    margin-top: 2rem;
  }
}

.text-h6 {
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.q-gutter-md {
  margin-top: 1rem;
}

.q-btn {
  margin-right: 1rem;
  &:last-child {
    margin-right: 0;
  }
}
</style>
