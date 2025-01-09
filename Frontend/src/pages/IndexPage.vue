<template>
  <q-page>
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container q-pa-md">
        <div class="row items-center">
          <div class="col-12 col-md-6 text-center text-md-left">
            <h1 class="text-h3 text-md-h2 text-weight-bold q-mb-md text-white">
              Ruhsal İyilik Yolculuğunuza Başlayın
            </h1>
            <p
              class="text-body1 text-md-h6 q-mb-lg text-white text-weight-medium"
              style="opacity: 0.95"
            >
              Yapay zeka destekli eşleştirme sistemiyle, kişiselleştirilmiş ruh sağlığı desteği için
              uzman terapistlerle bağlantı kurun.
            </p>
            <div class="q-gutter-md">
              <q-btn
                unelevated
                color="white"
                text-color="primary"
                size="md"
                class="full-width-mobile hero-btn"
                label="Hemen Başla"
                to="/auth/register"
              />
              <q-btn
                outline
                color="white"
                size="md"
                class="full-width-mobile hero-btn q-mt-sm"
                label="Daha Fazla Bilgi"
                @click="scrollToServices"
              />
            </div>
          </div>
          <div class="col-12 col-md-6 q-mt-md q-mt-md-none">
            <q-img src="/images/hero-illustration.svg" class="hero-image" />
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
    <section id="therapists" class="therapist-section">
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
              to="/therapist-register"
              push
            />
          </div>
        </div>
      </div>
    </section>
  </q-page>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'

const scrollToSection = (sectionId: string) => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({
      behavior: 'smooth',
      block: 'start',
    })
  }
}

const scrollToServices = () => {
  scrollToSection('services')
}

// Add mounted hook to handle initial hash navigation
onMounted(() => {
  // Check if there's a hash in the URL
  if (window.location.hash) {
    const sectionId = window.location.hash.slice(1)
    // Small delay to ensure the page is fully loaded
    setTimeout(() => {
      scrollToSection(sectionId)
    }, 100)
  }
})
</script>

<style lang="scss" scoped>
@use 'sass:color';

.hero-section {
  min-height: auto;
  padding: 4rem 0;
  background: linear-gradient(
    135deg,
    $gradient-start 0%,
    scale-color($gradient-start, $lightness: -15%) 100%
  );
  color: white;
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('/images/pattern.svg') repeat;
    opacity: 0.1;
    animation: float 20s linear infinite;
  }

  .container {
    position: relative;
    z-index: 2;
  }

  h1 {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    animation: fadeInUp 0.8s ease-out;
    font-size: 2.5rem;
    line-height: 1.2;

    @media (max-width: 599px) {
      font-size: 2rem;
    }
  }

  p {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    animation: fadeInUp 0.8s ease-out 0.2s both;
    font-size: 1.1rem;
    line-height: 1.5;

    @media (max-width: 599px) {
      font-size: 1rem;
    }
  }

  .hero-btn {
    font-weight: 600;
    border-width: 2px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: fadeInUp 0.8s ease-out 0.4s both;
    min-width: 150px;

    &.q-btn--unelevated {
      &:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba($primary, 0.25);
        background: white;
      }
    }

    &.q-btn--outline {
      border: 2px solid white;

      &:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 30px rgba(255, 255, 255, 0.2);
      }
    }
  }

  .hero-image {
    animation: float 6s ease-in-out infinite;
    max-width: 100%;
    height: auto;
  }
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.service-card {
  height: 100%;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: $generic-border-radius;
  box-shadow: $card-shadow;
  overflow: hidden;
  background: linear-gradient(135deg, $card-bg, $soft-bg);

  &:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba($primary, 0.2);
  }

  .q-card-section {
    padding: 2rem;
  }

  .text-h6 {
    color: $primary;
    margin-bottom: 1rem;
    position: relative;

    &::after {
      content: '';
      position: absolute;
      bottom: -0.5rem;
      left: 0;
      width: 40px;
      height: 3px;
      background: linear-gradient(90deg, $primary, $secondary);
      border-radius: 2px;
    }
  }
}

.step-card {
  padding: 2rem;
  background: white;
  border-radius: $generic-border-radius;
  box-shadow: $card-shadow;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba($primary, 0.15);
  }

  .step-number {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, $primary, $secondary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.2rem;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba($primary, 0.3);
  }
}

.feature-item {
  display: flex;
  align-items: center;
  font-size: 1.1rem;
  margin-bottom: 1rem;
  padding: 0.5rem;
  border-radius: $generic-border-radius;
  transition: all 0.3s ease;

  &:hover {
    background: $soft-bg;
    transform: translateX(5px);
  }

  .q-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
    color: $secondary;
  }
}

// Animations
@keyframes float {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
  100% {
    transform: translateY(0px);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

// Mobile specific styles
@media (max-width: 767px) {
  .hero-section {
    padding: 2rem 0;
    text-align: center;

    .full-width-mobile {
      width: 100%;
      margin: 0.5rem 0;
    }

    .hero-image {
      margin-top: 2rem;
      max-width: 80%;
      margin-left: auto;
      margin-right: auto;
    }

    .q-gutter-md {
      margin: -4px 0;

      > * {
        margin: 4px 0;
      }
    }
  }
}
</style>
