<template>
  <div>
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

    <!-- Mobile Navigation Drawer -->
    <q-drawer v-model="drawerOpen" side="right" bordered :width="250" class="bg-white mobile-nav">
      <q-list padding>
        <q-item clickable v-ripple @click="scrollToSection('services')" class="mobile-nav-item">
          <q-item-section>Nasıl Çalışır?</q-item-section>
        </q-item>
        <q-item clickable v-ripple @click="scrollToSection('therapists')" class="mobile-nav-item">
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
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

interface Language {
  code: 'tr' | 'en'
  label: string
  flag: string
}

const router = useRouter()
const drawerOpen = ref(false)
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

const toggleDrawer = () => {
  drawerOpen.value = !drawerOpen.value
}

const scrollToSection = async (sectionId: string) => {
  // Close the drawer
  drawerOpen.value = false

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
</script>

<style lang="scss" scoped>
.mobile-nav {
  .mobile-nav-item {
    border-radius: 12px;
    margin: 0.25rem 0;
    color: $grey-8;
    font-weight: 500;
    transition: all 0.3s ease;

    &.q-item--active {
      color: $primary;
      background: linear-gradient(135deg, rgba($primary, 0.1), rgba($secondary, 0.1));
    }

    &:hover {
      background: linear-gradient(135deg, rgba($primary, 0.05), rgba($secondary, 0.05));
      transform: translateX(4px);
    }
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
