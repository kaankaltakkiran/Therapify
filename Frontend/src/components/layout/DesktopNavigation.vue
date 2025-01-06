<template>
  <div class="gt-sm row items-center nav-links">
    <q-btn
      flat
      no-caps
      label="Nasıl Çalışır?"
      @click="scrollToSection('services')"
      class="nav-link"
    />
    <q-btn
      flat
      no-caps
      label="Terapistler İçin"
      @click="scrollToSection('therapists')"
      class="nav-link"
    />
    <q-btn flat no-caps label="Terapistlerimiz" to="/therapists" class="nav-link" />
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
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const scrollToSection = async (sectionId: string) => {
  // If we're not on the home page, navigate there first
  if (router.currentRoute.value.path !== '/') {
    await router.push('/')
    // Wait for the navigation and DOM update
    await new Promise((resolve) => setTimeout(resolve, 100))
  }

  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({
      behavior: 'smooth',
      block: 'start',
    })
  }
}

interface Language {
  code: 'tr' | 'en'
  label: string
  flag: string
}

const currentLangCode = ref<Language['code']>('tr')

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

const defaultLanguage: Language = languages[0]

const currentLanguage = computed((): Language => {
  const found = languages.find((lang) => lang.code === currentLangCode.value)
  return found ?? defaultLanguage
})

const changeLanguage = (langCode: Language['code']) => {
  currentLangCode.value = langCode
  localStorage.setItem('language', langCode)
}
</script>

<style lang="scss" scoped>
.nav-links {
  flex: 1;
  justify-content: center;
  margin-right: 2rem;

  .nav-link {
    margin: 0 0.5rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    color: $grey-8;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;

    &::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: -1;
    }

    &:hover {
      color: $primary;
      transform: translateY(-1px);

      &::before {
        opacity: 1;
      }
    }
  }
}

.auth-buttons {
  .auth-link {
    font-weight: 500;
    color: $grey-8;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

    &:hover {
      background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
      color: $primary;
      transform: translateY(-1px);
    }
  }

  .auth-cta {
    font-weight: 600;
    padding: 0.5rem 1.5rem;
    border-radius: 12px;
    background: linear-gradient(135deg, $primary, $secondary);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba($primary, 0.3);
    }
  }
}

.language-selector {
  margin: 0 0.5rem;
  font-weight: 500;
  padding: 0.5rem 1rem;
  color: $grey-8;
  border-radius: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

  &:hover {
    background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
    color: $primary;
    transform: translateY(-1px);
  }

  .q-btn__content {
    text-transform: none;
  }
}

:deep(.q-list) {
  min-width: 150px;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba($primary, 0.1);
  overflow: hidden;
}

:deep(.q-item) {
  min-height: 40px;
  transition: all 0.3s ease;

  &.q-item--active {
    color: $primary;
    background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
  }

  &:hover {
    background: linear-gradient(135deg, rgba($primary, 0.05), rgba($secondary, 0.05));
  }
}
</style>
