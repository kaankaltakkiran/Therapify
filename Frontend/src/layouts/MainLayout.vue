<template>
  <q-layout view="lHh LpR fFf">
    <!-- Header -->
    <q-header elevated class="bg-white text-dark">
      <q-toolbar class="container">
        <!-- Logo -->
        <q-toolbar-title class="logo-container">
          <q-btn flat no-caps to="/" class="text-h5 text-primary logo-btn"> Therapify </q-btn>
        </q-toolbar-title>

        <!-- Desktop Navigation -->
        <div class="gt-sm row items-center nav-links">
          <q-btn flat no-caps label="Nasıl Çalışır?" to="/#services" class="nav-link" />
          <q-btn flat no-caps label="Terapistler İçin" to="/#therapists" class="nav-link" />
          <q-btn flat no-caps label="Hakkımızda" to="/about" class="nav-link" />
          <q-btn flat no-caps label="İletişim" to="/contact" class="nav-link" />

          <!-- Language Selector -->
          <q-btn-dropdown flat no-caps :label="currentLanguage.label" class="language-selector">
            <q-list>
              <q-item
                v-for="lang in languages"
                :key="lang.code"
                clickable
                v-close-popup
                @click="changeLanguage(lang.code)"
                :active="currentLanguage.code === lang.code"
              >
                <q-item-section avatar>
                  <q-avatar size="20px">
                    <img :src="lang.flag" :alt="lang.label" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>{{ lang.label }}</q-item-section>
                <q-item-section side v-if="currentLanguage.code === lang.code">
                  <q-icon name="check" color="primary" />
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>

          <!-- Auth Buttons -->
          <div class="auth-buttons q-ml-md">
            <q-btn flat no-caps label="Giriş" to="/auth/login" class="auth-link q-mr-sm" />
            <q-btn
              unelevated
              no-caps
              label="Kayıt Ol"
              to="/auth/register"
              class="auth-cta"
              color="primary"
            />
          </div>
        </div>

        <!-- Mobile Menu Button -->
        <div class="lt-md">
          <!-- Language Selector for Mobile -->
          <q-btn-dropdown flat dense round icon="language" color="primary" class="q-mr-sm">
            <q-list>
              <q-item
                v-for="lang in languages"
                :key="lang.code"
                clickable
                v-close-popup
                @click="changeLanguage(lang.code)"
                :active="currentLanguage.code === lang.code"
              >
                <q-item-section avatar>
                  <q-avatar size="20px">
                    <img :src="lang.flag" :alt="lang.label" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>{{ lang.label }}</q-item-section>
                <q-item-section side v-if="currentLanguage.code === lang.code">
                  <q-icon name="check" color="primary" />
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>

          <q-btn flat round icon="menu" color="primary" @click="toggleDrawer" />
        </div>
      </q-toolbar>
    </q-header>

    <!-- Mobile Navigation Drawer -->
    <q-drawer v-model="drawerOpen" side="right" bordered :width="250" class="bg-white mobile-nav">
      <q-list padding>
        <q-item clickable v-ripple to="/#services" class="mobile-nav-item">
          <q-item-section>Nasıl Çalışır?</q-item-section>
        </q-item>
        <q-item clickable v-ripple to="/#therapists" class="mobile-nav-item">
          <q-item-section>Terapistler İçin</q-item-section>
        </q-item>
        <q-item clickable v-ripple to="/about" class="mobile-nav-item">
          <q-item-section>Hakkımızda</q-item-section>
        </q-item>
        <q-item clickable v-ripple to="/contact" class="mobile-nav-item">
          <q-item-section>İletişim</q-item-section>
        </q-item>
        <q-separator class="q-my-md" />
        <q-item clickable v-ripple to="/auth/login" class="mobile-nav-item">
          <q-item-section>Giriş</q-item-section>
        </q-item>
        <q-item clickable v-ripple to="/auth/register" class="mobile-nav-item">
          <q-item-section>Kayıt Ol</q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <!-- Page Content -->
    <q-page-container>
      <router-view />
    </q-page-container>

    <!-- Footer -->
    <q-footer class="bg-white text-dark absolute-bottom footer">
      <div class="container q-pa-lg">
        <div class="row q-col-gutter-lg">
          <!-- Company Info -->
          <div class="col-12 col-md-4 q-pb-md">
            <div class="text-h6 text-primary q-mb-md">Therapify</div>
            <p class="text-grey-8">
              Yapay zeka destekli eşleştirme ile ruh sağlığı uzmanlarıyla sizi buluşturuyoruz.
            </p>
            <div class="row q-gutter-sm">
              <q-btn flat round color="grey-8" icon="fab fa-facebook" class="social-btn" />
              <q-btn flat round color="grey-8" icon="fab fa-twitter" class="social-btn" />
              <q-btn flat round color="grey-8" icon="fab fa-instagram" class="social-btn" />
              <q-btn flat round color="grey-8" icon="fab fa-linkedin" class="social-btn" />
            </div>
          </div>

          <!-- Quick Links -->
          <div class="col-12 col-md-2">
            <div class="text-subtitle1 q-mb-md">Hızlı Bağlantılar</div>
            <q-list dense padding>
              <q-item clickable v-ripple to="/about">
                <q-item-section>Hakkımızda</q-item-section>
              </q-item>
              <q-item clickable v-ripple to="/contact">
                <q-item-section>İletişim</q-item-section>
              </q-item>
              <q-item clickable v-ripple to="/blog">
                <q-item-section>Blog</q-item-section>
              </q-item>
              <q-item clickable v-ripple to="/careers">
                <q-item-section>Kariyer</q-item-section>
              </q-item>
            </q-list>
          </div>

          <!-- Support -->
          <div class="col-12 col-md-2">
            <div class="text-subtitle1 q-mb-md">Destek</div>
            <q-list dense padding>
              <q-item clickable v-ripple to="/help">
                <q-item-section>Yardım Merkezi</q-item-section>
              </q-item>
              <q-item clickable v-ripple to="/privacy">
                <q-item-section>Gizlilik Politikası</q-item-section>
              </q-item>
              <q-item clickable v-ripple to="/terms">
                <q-item-section>Kullanım Koşulları</q-item-section>
              </q-item>
              <q-item clickable v-ripple to="/faq">
                <q-item-section>SSS</q-item-section>
              </q-item>
            </q-list>
          </div>

          <!-- Contact Info -->
          <div class="col-12 col-md-4">
            <div class="text-subtitle1 q-mb-md">İletişim</div>
            <q-list dense padding>
              <q-item>
                <q-item-section avatar>
                  <q-icon name="email" />
                </q-item-section>
                <q-item-section>destek@therapify.com</q-item-section>
              </q-item>
              <q-item>
                <q-item-section avatar>
                  <q-icon name="phone" />
                </q-item-section>
                <q-item-section>+90 (555) 123-4567</q-item-section>
              </q-item>
              <q-item>
                <q-item-section avatar>
                  <q-icon name="location_on" />
                </q-item-section>
                <q-item-section>
                  Terapi Sokak No: 123<br />
                  Ruh Sağlığı Mahallesi, İstanbul
                </q-item-section>
              </q-item>
            </q-list>
          </div>
        </div>

        <!-- Copyright -->
        <q-separator class="q-my-md" />

        <div class="row justify-between items-center copyright-section">
          <div class="col-12 col-md-auto text-center text-md-left q-mb-sm-md">
            © {{ new Date().getFullYear() }} Therapify. Tüm hakları saklıdır.
          </div>
          <div class="col-12 col-md-auto text-center text-md-right">
            <q-btn flat no-caps label="Gizlilik Politikası" to="/privacy" class="footer-link" />
            <q-btn flat no-caps label="Kullanım Koşulları" to="/terms" class="footer-link" />
          </div>
        </div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface Language {
  code: 'tr' | 'en'
  label: string
  flag: string
}

const drawerOpen = ref(false)
const currentLangCode = ref<Language['code']>('tr') // Default language is Turkish

// Language options with default language first
const languages = [
  {
    code: 'tr',
    label: 'Türkçe',
    flag: '/images/flags/tr.svg',
  },
  {
    code: 'en',
    label: 'English',
    flag: '/images/flags/en.svg',
  },
] as const satisfies readonly Language[]

// Get default language
const defaultLanguage: Language = languages[0]

// Computed property for current language with type safety
const currentLanguage = computed((): Language => {
  const found = languages.find((lang) => lang.code === currentLangCode.value)
  return found ?? defaultLanguage
})

const changeLanguage = (langCode: Language['code']) => {
  currentLangCode.value = langCode
  localStorage.setItem('language', langCode)
}

const toggleDrawer = () => {
  drawerOpen.value = !drawerOpen.value
}
</script>

<style lang="scss" scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.q-header {
  border-bottom: 1px solid $grey-3;

  .q-toolbar {
    height: 70px;
  }
}

.logo-container {
  flex: 0 0 auto;
  .logo-btn {
    font-weight: 600;
    padding: 0;
    letter-spacing: -0.5px;
  }
}

.nav-links {
  flex: 1;
  justify-content: center;
  margin-right: 2rem;

  .nav-link {
    margin: 0 0.5rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    color: $grey-8;
    border-radius: 8px;
    transition: all 0.3s ease;

    &:hover {
      background: rgba($primary, 0.1);
      color: $primary;
    }
  }
}

.auth-buttons {
  .auth-link {
    font-weight: 500;
    color: $grey-8;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;

    &:hover {
      background: rgba($primary, 0.1);
      color: $primary;
    }
  }

  .auth-cta {
    font-weight: 600;
    padding: 0.5rem 1.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;

    &:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba($primary, 0.2);
    }
  }
}

.mobile-nav {
  .mobile-nav-item {
    border-radius: 8px;
    margin: 0.25rem 0;
    color: $grey-8;
    font-weight: 500;

    &.q-item--active {
      color: $primary;
      background: rgba($primary, 0.1);
    }
  }
}

.footer {
  border-top: 1px solid $grey-3;

  .social-btn {
    transition: all 0.3s ease;

    &:hover {
      background: rgba($primary, 0.1);
      color: $primary;
      transform: translateY(-2px);
    }
  }

  .footer-link {
    color: $grey-7;
    transition: color 0.3s ease;
    padding: 0.5rem;
    border-radius: 4px;
    font-weight: 500;

    &:hover {
      color: $primary;
      background: rgba($primary, 0.1);
    }
  }
}

.copyright-section {
  @media (max-width: 767px) {
    flex-direction: column;
    text-align: center;

    .col-12 {
      margin-bottom: 1rem;
    }
  }
}

// Responsive adjustments
@media (max-width: 1200px) {
  .container {
    padding: 0 2rem;
  }
}

@media (max-width: 767px) {
  .container {
    padding: 0 1rem;
  }

  .q-footer {
    .q-pa-lg {
      padding: 1rem;
    }
  }
}

// Add min-height to page container to ensure footer stays at bottom
:deep(.q-page-container) {
  min-height: calc(100vh - 70px);
  display: flex;
  flex-direction: column;

  .q-page {
    flex: 1 0 auto;
  }
}

.language-selector {
  margin: 0 0.5rem;
  font-weight: 500;
  padding: 0.5rem 1rem;
  color: $grey-8;
  border-radius: 8px;
  transition: all 0.3s ease;

  &:hover {
    background: rgba($primary, 0.1);
    color: $primary;
  }

  .q-btn__content {
    text-transform: none;
  }
}

// Add these styles for the language dropdown
:deep(.q-list) {
  min-width: 150px;
}

:deep(.q-item) {
  min-height: 40px;

  &.q-item--active {
    color: $primary;
    background: rgba($primary, 0.1);
  }
}
</style>
